<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\IR;

use PdfGenerator\Font\Frontend\File\FontFile;
use PdfGenerator\Font\Frontend\File\Table\CMap\FormatReader;
use PdfGenerator\Font\Frontend\File\Table\CMap\Subtable;
use PdfGenerator\Font\Frontend\File\Table\CMapTable;
use PdfGenerator\Font\Frontend\File\Table\HMtx\LongHorMetric;
use PdfGenerator\Font\Frontend\File\Table\HMtxTable;
use PdfGenerator\Font\Frontend\File\Traits\BoundingBoxTrait;
use PdfGenerator\Font\Frontend\FileReader;
use PdfGenerator\Font\Frontend\StreamReader;
use PdfGenerator\Font\IR\Structure\BoundingBox;
use PdfGenerator\Font\IR\Structure\Character;
use PdfGenerator\Font\IR\Structure\Font;
use PdfGenerator\Font\IR\Structure\PostScriptInfo;
use PdfGenerator\Font\IR\Structure\TableDirectory;
use PdfGenerator\Font\IR\Structure\Tables\FontInformation;
use PdfGenerator\Font\IR\Utils\CMap\GlyphIndexFormatVisitor;
use PdfGenerator\Font\Resources\GlyphNameMapping\Factory;

class Parser
{
    /**
     * @var GlyphIndexFormatVisitor
     */
    private $cMapGlyphIndexFormatVisitor;

    /**
     * @var Utils\Post\GlyphIndexFormatVisitor
     */
    private $postGlyphIndexFormatVisitor;

    /**
     * @var Factory
     */
    private $glyphNameMappingFactory;

    /**
     * Parser constructor.
     */
    public function __construct(GlyphIndexFormatVisitor $cMapGlyphIndexFormatVisitor, Utils\Post\GlyphIndexFormatVisitor $postGlyphIndexFormatVisitor, Factory $glyphNameMappingFactory)
    {
        $this->cMapGlyphIndexFormatVisitor = $cMapGlyphIndexFormatVisitor;
        $this->postGlyphIndexFormatVisitor = $postGlyphIndexFormatVisitor;
        $this->glyphNameMappingFactory = $glyphNameMappingFactory;
    }

    /**
     * @return Parser
     */
    public static function create()
    {
        $factory = new Factory();
        $cMapFormatVisitor = new GlyphIndexFormatVisitor();
        $postFormatVisitor = new Utils\Post\GlyphIndexFormatVisitor($factory);

        return new self($cMapFormatVisitor, $postFormatVisitor, $factory);
    }

    /**
     * @throws \Exception
     */
    public function parse(string $content): Font
    {
        $streamReader = new StreamReader($content);
        $cMapFormatReader = new FormatReader();
        $postFormatReader = new \PdfGenerator\Font\Frontend\File\Table\Post\FormatReader();
        $fileReader = new FileReader($cMapFormatReader, $postFormatReader);

        $fontFile = $fileReader->read($streamReader);
        $font = $this->createFont($fontFile);

        return $font;
    }

    /**
     * @throws \Exception
     */
    private function createFont(FontFile $fontFile): Font
    {
        $font = new Font();
        $font->setTableDirectory($this->createTableDirectory($fontFile));
        $font->setFontInformation($this->createFontInformation($fontFile));

        $characters = $this->createCharacters($fontFile);
        $this->addGlyphInfo($characters, $fontFile);

        // some of the first glyphs might be strange, following one or the other convention
        // https://github.com/fontforge/fontforge/issues/796
        // like first glyphs must be .notdef, second null, third CR, ...
        // the first char is always the .notdef, afterwards the conventions might diverge
        // we detect the convention-specific chars by assuming they have no unicode assigned
        $reservedCharacters = [array_shift($characters)];
        while (\count($characters) > 0 && $characters[0]->getUnicodePoint() === null) {
            $reservedCharacters[] = array_shift($characters);
        }

        $font->setReservedCharacters($reservedCharacters);
        $font->setCharacters($characters);

        return $font;
    }

    /**
     * @return TableDirectory
     */
    private function createTableDirectory(FontFile $fontFile)
    {
        $tableDirectory = new TableDirectory();

        $tableDirectory->setCvtTable($fontFile->getCvtTable());
        $tableDirectory->setFpgmTable($fontFile->getFpgmTable());
        $tableDirectory->setGaspTable($fontFile->getGaspTable());
        $tableDirectory->setGdefTable($fontFile->getGDEFTable());
        $tableDirectory->setGposTable($fontFile->getGPOSTable());
        $tableDirectory->setGsubTable($fontFile->getGSUBTable());
        $tableDirectory->setHeadTable($fontFile->getHeadTable());
        $tableDirectory->setHHeaTable($fontFile->getHHeaTable());
        $tableDirectory->setMaxPTable($fontFile->getMaxPTable());
        $tableDirectory->setNameTable($fontFile->getNameTable());
        $tableDirectory->setOs2Table($fontFile->getOS2Table());
        $tableDirectory->setPostTable($fontFile->getPostTable());
        $tableDirectory->setPrepTable($fontFile->getPrepTable());
        $tableDirectory->setRawTables($fontFile->getRawTables());

        return $tableDirectory;
    }

    /**
     * @param Character[] $characters
     *
     * @throws \Exception
     */
    private function addGlyphInfo(array $characters, FontFile $fontFile)
    {
        $subtable = $this->chooseBestCMapSubtable($fontFile->getCMapTable());

        $cMapMapping = $this->cMapGlyphIndexFormatVisitor->visitFormat($subtable->getFormat());
        $postMapping = $this->postGlyphIndexFormatVisitor->visitFormat($fontFile->getPostTable()->getFormat());
        $aGLFMapping = $this->glyphNameMappingFactory->getAGLFMapping();

        foreach ($characters as $characterIndex => $character) {
            if (!\array_key_exists($characterIndex, $postMapping)) {
                continue;
            }

            $glyphInfo = $postMapping[$characterIndex];

            $postScriptInfo = new PostScriptInfo();
            $postScriptInfo->setMacintoshGlyphIndex($glyphInfo->getMacintoshIndex());
            $postScriptInfo->setName($glyphInfo->getName());

            $character->setPostScriptInfo($postScriptInfo);
        }

        foreach ($cMapMapping as $unicode => $characterIndex) {
            if (!\array_key_exists($characterIndex, $characters)) {
                continue;
            }

            $character = $characters[$characterIndex];
            $character->setUnicodePoint($unicode);

            if (\array_key_exists($unicode, $aGLFMapping)) {
                $aGLFInfo = $aGLFMapping[$unicode];
                $character->getPostScriptInfo()->setName($aGLFInfo);
            }
        }
    }

    /**
     * @return Subtable
     */
    private function chooseBestCMapSubtable(CMapTable $cMapTable)
    {
        /** @var Subtable[] $cMapSubtable */
        $cMapSubtable = [];

        $overflow = 20;
        foreach ($cMapTable->getSubtables() as $subtable) {
            // we prefer unicode over anything else
            if ($subtable->getPlatformID() === 0) {
                if ($subtable->getPlatformSpecificID() <= 4) {
                    // we prefer platform 4, then 3, then 2, then 1
                    $cMapSubtable[4 - $subtable->getPlatformSpecificID()] = $subtable;
                    continue;
                }
            }

            if ($subtable->getPlatformID() === 3) {
                $cMapSubtable[10 + 10 - $subtable->getPlatformSpecificID()] = $subtable;
                continue;
            }

            $cMapSubtable[$overflow++] = $subtable;
        }

        $minimalKey = min(array_keys($cMapSubtable));

        return $cMapSubtable[$minimalKey];
    }

    /**
     * @return Character[]
     */
    private function createCharacters(FontFile $fontFile): array
    {
        $characters = [];

        foreach ($fontFile->getGlyfTables() as $index => $glyfTable) {
            $character = new Character();
            $character->setGlyfTable($glyfTable);

            $longHorMetric = $this->getLongHorMetric($fontFile->getHMtxTable(), $index);
            $character->setLongHorMetric($longHorMetric);

            if ($glyfTable !== null) {
                $character->setGlyfTable($glyfTable);

                $boundingBox = $this->calculateBoundingBox($glyfTable);
                $character->setBoundingBox($boundingBox);
            }

            $characters[$index] = $character;
        }

        foreach ($characters as $character) {
            if (!$character->getGlyfTable()) {
                continue;
            }

            foreach ($character->getGlyfTable()->getComponentGlyphs() as $componentGlyph) {
                $componentCharacter = \array_key_exists($componentGlyph->getGlyphIndex(), $characters) ? $characters[$componentGlyph->getGlyphIndex()] : null;
                $character->addComponentCharacter($componentCharacter);
            }
        }

        return $characters;
    }

    private function getLongHorMetric(HMtxTable $hMtxTable, int $entryIndex): LongHorMetric
    {
        $longHorMetricCount = \count($hMtxTable->getLongHorMetrics());
        if ($entryIndex < $longHorMetricCount) {
            return $hMtxTable->getLongHorMetrics()[$entryIndex];
        }

        $lastEntry = $hMtxTable->getLongHorMetrics()[$longHorMetricCount - 1];
        $bearingIndex = $entryIndex % $longHorMetricCount;
        $bearingEntry = $hMtxTable->getLeftSideBearings()[$bearingIndex];

        $longHorMetric = new LongHorMetric();
        $longHorMetric->setAdvanceWidth($lastEntry->getAdvanceWidth());
        $longHorMetric->setLeftSideBearing($bearingEntry);

        return $longHorMetric;
    }

    /**
     * @param BoundingBoxTrait $boundingBoxTrait
     *
     * @return BoundingBox
     */
    private function calculateBoundingBox($boundingBoxTrait)
    {
        $boundingBox = new BoundingBox();

        $boundingBox->setHeight((float)($boundingBoxTrait->getYMax() - $boundingBoxTrait->getYMin()));
        $boundingBox->setWidth((float)($boundingBoxTrait->getXMax() - $boundingBoxTrait->getXMin()));

        return $boundingBox;
    }

    /**
     * @param FontInformation $fontFile
     */
    private function createFontInformation(FontFile $fontFile)
    {
        $fontInformation = new FontInformation();

        foreach ($fontFile->getNameTable()->getNameRecords() as $nameRecord) {
            $value = $nameRecord->getValue();
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-16BE');

            switch ($nameRecord->getNameID()) {
                case 0:
                    $fontInformation->setCopyrightNotice($value);
                    break;
                case 1:
                    $fontInformation->setFamily($value);
                    break;
                case 2:
                    $fontInformation->setSubfamily($value);
                    break;
                case 3:
                    $fontInformation->setIdentifier($value);
                    break;
                case 4:
                    $fontInformation->setFullName($value);
                    break;
                case 5:
                    $fontInformation->setVersion($value);
                    break;
                case 6:
                    $fontInformation->setPostScriptName($value);
                    break;
                case 7:
                    $fontInformation->setTrademarkNotice($value);
                    break;
                case 8:
                    $fontInformation->setManufacturer($value);
                    break;
                case 9:
                    $fontInformation->setDesigner($value);
                    break;
                case 10:
                    $fontInformation->setDescription($value);
                    break;
                case 11:
                    $fontInformation->setUrlVendor($value);
                    break;
                case 12:
                    $fontInformation->setUrlDesigner($value);
                    break;
                case 13:
                    $fontInformation->setLicenseDescription($value);
                    break;
                case 14:
                    $fontInformation->setLicenseUrl($value);
                    break;
                case 16:
                    $fontInformation->setTypographicFamily($value);
                    break;
                case 17:
                    $fontInformation->setTypographicSubfamily($value);
                    break;
                case 18:
                    $fontInformation->setCompatibleFull($value);
                    break;
                case 19:
                    $fontInformation->setSampleText($value);
                    break;
                case 20:
                    $fontInformation->setPostScriptCIDName($value);
                    break;
                case 21:
                    $fontInformation->setWwsFamilyName($value);
                    break;
                case 22:
                    $fontInformation->setWwsSubfamilyName($value);
                    break;
            }
        }

        return $fontInformation;
    }
}
