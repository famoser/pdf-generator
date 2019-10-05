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
use PdfGenerator\Font\IR\Utils\Post\GlyphInfo;
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
     *
     * @param GlyphIndexFormatVisitor $cMapGlyphIndexFormatVisitor
     * @param Utils\Post\GlyphIndexFormatVisitor $postGlyphIndexFormatVisitor
     * @param Factory $glyphNameMappingFactory
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
     * @param string $content
     *
     * @throws \Exception
     *
     * @return Font
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
     * @param FontFile $fontFile
     *
     * @throws \Exception
     *
     * @return Font
     */
    private function createFont(FontFile $fontFile): Font
    {
        $font = new Font();
        $font->setTableDirectory($this->createTableDirectory($fontFile));
        $font->setFontInformation($this->createFontInformation($fontFile));

        $characters = $this->createCharacters($fontFile);
        $mappedCharacters = $this->mapCharacters($characters, $fontFile);

        // ensure first character is .notdef character with unicode point 0
        if ($mappedCharacters[0]->getUnicodePoint() !== 0) {
            $missingGlyphCharacter = $characters[0];
            $missingGlyphCharacter->setUnicodePoint(0);
            array_unshift($mappedCharacters, $missingGlyphCharacter);
        }

        $font->setCharacters($mappedCharacters);

        return $font;
    }

    /**
     * @param FontFile $fontFile
     *
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
     * @param FontFile $fontFile
     *
     * @throws \Exception
     *
     * @return Character[]
     */
    private function mapCharacters(array $characters, FontFile $fontFile)
    {
        $subtable = $this->chooseBestCMapSubtable($fontFile->getCMapTable());

        $cMapMapping = $this->cMapGlyphIndexFormatVisitor->visitFormat($subtable->getFormat());
        $postMapping = $this->postGlyphIndexFormatVisitor->visitFormat($fontFile->getPostTable()->getFormat());
        $aGLFMapping = $this->glyphNameMappingFactory->getAGLFMapping();

        $mappedCharacters = [];
        foreach ($cMapMapping as $unicode => $characterIndex) {
            $character = $characters[$characterIndex];
            if ($character !== null) {
                $character->setUnicodePoint($unicode);

                $glyphInfo = \array_key_exists($characterIndex, $postMapping) ? $postMapping[$characterIndex] : null;
                $aGLFInfo = \array_key_exists($unicode, $aGLFMapping) ? $aGLFMapping[$unicode] : null;
                $postScriptInfo = $this->getPostScriptInfo($glyphInfo, $aGLFInfo);
                $character->setPostScriptInfo($postScriptInfo);

                $mappedCharacters[] = $character;
            }
        }

        return $mappedCharacters;
    }

    /**
     * @param CMapTable $cMapTable
     *
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
     * @param FontFile $fontFile
     *
     * @return Character[]
     */
    private function createCharacters(FontFile $fontFile): array
    {
        $characters = [];

        $characterCount = \count($fontFile->getGlyfTables());
        for ($i = 0; $i < $characterCount; ++$i) {
            $glyfTable = $fontFile->getGlyfTables()[$i];
            if ($glyfTable === null) {
                $characters[] = null;
                continue;
            }

            $character = new Character();
            $character->setGlyfTable($glyfTable);

            $longHorMetric = $this->getLongHorMetric($fontFile->getHMtxTable(), $i);
            $character->setLongHorMetric($longHorMetric);

            $boundingBox = $this->calculateBoundingBox($glyfTable, $fontFile->getHeadTable()->getUnitsPerEm());
            $character->setBoundingBox($boundingBox);

            $characters[] = $character;
        }

        return $characters;
    }

    /**
     * @param HMtxTable $hMtxTable
     * @param int $entryIndex
     *
     * @return LongHorMetric
     */
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
     * @param int $divisor
     *
     * @return BoundingBox
     */
    private function calculateBoundingBox($boundingBoxTrait, int $divisor)
    {
        $boundingBox = new BoundingBox();

        $boundingBox->setHeight((float)($boundingBoxTrait->getYMax() - $boundingBoxTrait->getYMin()) / $divisor);
        $boundingBox->setWidth(((float)$boundingBoxTrait->getXMax() - $boundingBoxTrait->getXMin()) / $divisor);

        return $boundingBox;
    }

    /**
     * @param GlyphInfo|null $glyphInfo
     * @param string|null $aGLFName
     *
     * @return PostScriptInfo
     */
    private function getPostScriptInfo(?GlyphInfo $glyphInfo, ?string $aGLFName)
    {
        $postScriptInfo = new PostScriptInfo();

        if ($glyphInfo === null && $aGLFName === null) {
            $postScriptInfo->setName('.notdef');
            $postScriptInfo->setMacintoshGlyphIndex(0);
        } else {
            if ($glyphInfo === null) {
                $postScriptInfo->setName($aGLFName);
            } else {
                $postScriptInfo->setMacintoshGlyphIndex($glyphInfo->getMacintoshIndex());
                $postScriptInfo->setName($glyphInfo->getName());
            }
        }

        return $postScriptInfo;
    }

    /**
     * @param FontInformation $fontFile
     */
    private function createFontInformation(FontFile $fontFile)
    {
        $fontInformation = new FontInformation();

        foreach ($fontFile->getNameTable()->getNameRecords() as $nameRecord) {
            $value = $nameRecord->getValue();

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
