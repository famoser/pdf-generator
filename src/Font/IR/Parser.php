<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Font\IR;

use Famoser\PdfGenerator\Font\Frontend\File\FontFile;
use Famoser\PdfGenerator\Font\Frontend\File\Table\CMap\FormatReader;
use Famoser\PdfGenerator\Font\Frontend\File\Table\CMap\Subtable;
use Famoser\PdfGenerator\Font\Frontend\File\Table\CMapTable;
use Famoser\PdfGenerator\Font\Frontend\File\Table\HMtx\LongHorMetric;
use Famoser\PdfGenerator\Font\Frontend\File\Table\HMtxTable;
use Famoser\PdfGenerator\Font\Frontend\FileReader;
use Famoser\PdfGenerator\Font\Frontend\StreamReader;
use Famoser\PdfGenerator\Font\IR\Structure\BoundingBox;
use Famoser\PdfGenerator\Font\IR\Structure\Character;
use Famoser\PdfGenerator\Font\IR\Structure\Font;
use Famoser\PdfGenerator\Font\IR\Structure\PostScriptInfo;
use Famoser\PdfGenerator\Font\IR\Structure\TableDirectory;
use Famoser\PdfGenerator\Font\IR\Structure\Tables\FontInformation;
use Famoser\PdfGenerator\Font\IR\Utils\CMap\GlyphIndexFormatVisitor;
use Famoser\PdfGenerator\Font\Resources\GlyphNameMapping\Factory;

readonly class Parser
{
    public function __construct(private GlyphIndexFormatVisitor $cMapGlyphIndexFormatVisitor, private Utils\Post\GlyphIndexFormatVisitor $postGlyphIndexFormatVisitor, private Factory $glyphNameMappingFactory)
    {
    }

    public static function create(): Parser
    {
        $factory = new Factory();
        $cMapFormatVisitor = new GlyphIndexFormatVisitor();
        $postFormatVisitor = new Utils\Post\GlyphIndexFormatVisitor($factory);

        return new self($cMapFormatVisitor, $postFormatVisitor, $factory);
    }

    public function parse(string $content): Font
    {
        $streamReader = new StreamReader($content);
        $cMapFormatReader = new FormatReader();
        $postFormatReader = new \Famoser\PdfGenerator\Font\Frontend\File\Table\Post\FormatReader();
        $fileReader = new FileReader($cMapFormatReader, $postFormatReader);

        $fontFile = $fileReader->read($streamReader);

        return $this->createFont($fontFile);
    }

    private function createFont(FontFile $fontFile): Font
    {
        $font = new Font();
        $font->setIsTrueTypeFont($fontFile->getIsTrueTypeFile());
        $font->setTableDirectory($this->createTableDirectory($fontFile));
        $font->setFontInformation($this->createFontInformation($fontFile));

        $characters = $this->createCharacters($fontFile);
        $this->addGlyphInfo($characters, $fontFile);

        // some of the first glyphs might be strange, following one or the other convention
        // https://github.com/fontforge/fontforge/issues/796
        // like first glyphs must be .notdef, second null, third CR, ...
        // the first char is always the .notdef, afterward the conventions might diverge
        // we detect the convention-specific chars by assuming they have no unicode assigned
        $reservedCharacters = [array_shift($characters)];
        while (\count($characters) > 0 && null === $characters[0]->getUnicodePoint()) {
            $reservedCharacters[] = array_shift($characters);
        }

        $font->setReservedCharacters($reservedCharacters);
        $font->setCharacters($characters);

        return $font;
    }

    private function createTableDirectory(FontFile $fontFile): TableDirectory
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
     */
    private function addGlyphInfo(array $characters, FontFile $fontFile): void
    {
        $subtable = $this->chooseBestCMapSubtable($fontFile->getCMapTable());

        $cMapMapping = $subtable->getFormat()->accept($this->cMapGlyphIndexFormatVisitor);
        $postMapping = $fontFile->getPostTable()->getFormat()->accept($this->postGlyphIndexFormatVisitor);
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

            if (\array_key_exists($unicode, $aGLFMapping) && $character->getPostScriptInfo()) {
                $aGLFInfo = $aGLFMapping[$unicode];
                $character->getPostScriptInfo()->setName($aGLFInfo);
            }
        }
    }

    private function chooseBestCMapSubtable(CMapTable $cMapTable): Subtable
    {
        /** @var Subtable[] $cMapSubtable */
        $cMapSubtable = [];

        $overflow = 20;
        foreach ($cMapTable->getSubtables() as $subtable) {
            // we prefer unicode over anything else
            if (0 === $subtable->getPlatformID()) {
                if ($subtable->getPlatformSpecificID() <= 4) {
                    // we prefer platform 4, then 3, then 2, then 1
                    $cMapSubtable[4 - $subtable->getPlatformSpecificID()] = $subtable;
                    continue;
                }
            }

            if (3 === $subtable->getPlatformID()) {
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

        $glyphCount = $fontFile->getMaxPTable()->getNumGlyphs();
        $glyphTableCount = \count($fontFile->getGlyfTables());
        for ($i = 0; $i < $glyphCount; ++$i) {
            $character = new Character();

            if ($i < $glyphTableCount) {
                $character->setGlyfTable($fontFile->getGlyfTables()[$i]);

                if ($character->getGlyfTable()) {
                    $boundingBox = new BoundingBox();
                    $boundingBox->setHeight((float) ($character->getGlyfTable()->getYMax() - $character->getGlyfTable()->getYMin()));
                    $boundingBox->setWidth((float) ($character->getGlyfTable()->getXMax() - $character->getGlyfTable()->getXMin()));
                    $character->setBoundingBox($boundingBox);
                }
            }

            $longHorMetric = $this->getLongHorMetric($fontFile->getHMtxTable(), $i);
            $character->setLongHorMetric($longHorMetric);

            $characters[] = $character;
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

    private function createFontInformation(FontFile $fontFile): FontInformation
    {
        $fontInformation = new FontInformation();

        foreach ($fontFile->getNameTable()->getNameRecords() as $nameRecord) {
            $value = $nameRecord->getValue();
            /** @var string $value */
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
