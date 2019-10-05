<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Backend;

use PdfGenerator\Font\Backend\File\Table\Base\BaseTable;
use PdfGenerator\Font\Backend\File\Table\CMap\Format\Format4;
use PdfGenerator\Font\Backend\File\Table\CMap\Subtable;
use PdfGenerator\Font\Backend\File\Table\CMapTable;
use PdfGenerator\Font\Backend\File\Table\GlyfTable;
use PdfGenerator\Font\Backend\File\Table\HeadTable;
use PdfGenerator\Font\Backend\File\Table\HHeaTable;
use PdfGenerator\Font\Backend\File\Table\HMtx\LongHorMetric;
use PdfGenerator\Font\Backend\File\Table\HMtxTable;
use PdfGenerator\Font\Backend\File\Table\LocaTable;
use PdfGenerator\Font\Backend\File\Table\MaxPTable;
use PdfGenerator\Font\Backend\File\Table\Name\NameRecord;
use PdfGenerator\Font\Backend\File\Table\NameTable;
use PdfGenerator\Font\Backend\File\Table\OffsetTable;
use PdfGenerator\Font\Backend\File\Table\Post\Format\Format2;
use PdfGenerator\Font\Backend\File\Table\PostTable;
use PdfGenerator\Font\Backend\File\Table\RawTable;
use PdfGenerator\Font\Backend\File\Table\TableDirectoryEntry;
use PdfGenerator\Font\Backend\File\TableDirectory;
use PdfGenerator\Font\Backend\File\TableVisitor;
use PdfGenerator\Font\Backend\File\Traits\BinaryTreeSearchableTrait;
use PdfGenerator\Font\IR\Structure\Character;
use PdfGenerator\Font\IR\Structure\Font;
use PdfGenerator\Font\IR\Utils\CMap\Format4\Segment;

class FileWriter
{
    /**
     * @var TableVisitor
     */
    private $tableVisitor;

    /**
     * FileWriter constructor.
     *
     * @param TableVisitor $tableVisitor
     */
    public function __construct(TableVisitor $tableVisitor)
    {
        $this->tableVisitor = $tableVisitor;
    }

    /**
     * @return FileWriter
     */
    public static function create()
    {
        $tableVisitor = TableVisitor::create();

        return new self($tableVisitor);
    }

    /**
     * @param Font $font
     *
     * @return string
     * @throws \Exception
     *
     */
    public function writeFont(Font $font)
    {
        $tableDirectory = $this->createTableDirectory($font);

        return $this->writeTableDirectory($tableDirectory);
    }

    /**
     * @param Character[] $characters
     * @param Character $missingGlyphCharacter
     *
     * @return Character[]
     */
    private function prepareCharacters(array $characters, Character $missingGlyphCharacter)
    {
        $orderedCharacters = $this->sortCharactersByCodePoint($characters);

        array_unshift($orderedCharacters, $missingGlyphCharacter);

        return $orderedCharacters;
    }

    /**
     * @param \PdfGenerator\Font\Frontend\File\Table\HeadTable $source
     * @param Character[] $characters
     *
     * @return HeadTable
     */
    private function generateHeadTable(\PdfGenerator\Font\Frontend\File\Table\HeadTable $source, array $characters)
    {
        $headTable = new HeadTable();

        $headTable->setMajorVersion($source->getMajorVersion());
        $headTable->setMinorVersion($source->getMinorVersion());
        $headTable->setFontRevision($source->getFontRevision());

        // skip the checksum calculation
        $headTable->setCheckSumAdjustment(0);
        $headTable->setMagicNumber(0x5F0F3CF5);
        $headTable->setFlags($source->getFlags());
        $headTable->setUnitsPerEm($source->getUnitsPerEm());
        $headTable->setCreated($source->getCreated());
        $headTable->setModified($source->getModified());

        $xMin = 0;
        $yMin = 0;
        $xMax = PHP_INT_MAX;
        $yMax = PHP_INT_MAX;
        foreach ($characters as $character) {
            $xMin = min($character->getGlyfTable()->getXMin(), $xMin);
            $yMin = min($character->getGlyfTable()->getYMin(), $yMin);
            $xMax = max($character->getGlyfTable()->getXMax(), $xMax);
            $yMax = max($character->getGlyfTable()->getYMax(), $yMax);
        }

        $headTable->setXMin($xMin);
        $headTable->setYMin($yMin);
        $headTable->setXMax($xMax);
        $headTable->setYMax($yMax);

        $headTable->setMacStyle($source->getMacStyle());
        $headTable->setLowestRecPPEM($source->getLowestRecPPEM());
        $headTable->setFontDirectionHints($source->getFontDirectionHints());
        $headTable->setIndexToLocFormat(TableVisitor::$indexToLocFormat);
        $headTable->setGlyphDataFormat(0);

        return $headTable;
    }

    /**
     * @param \PdfGenerator\Font\Frontend\File\Table\HHeaTable $source
     * @param Character[] $characters
     * @param int $longHorMetricCount
     *
     * @return HHeaTable
     */
    private function generateHHeaTable(\PdfGenerator\Font\Frontend\File\Table\HHeaTable $source, array $characters, int $longHorMetricCount)
    {
        $hHeaTable = new HHeaTable();

        $hHeaTable->setVersion(1.0);
        $hHeaTable->setAscent($source->getAscent());
        $hHeaTable->setDecent($source->getDecent());
        $hHeaTable->setLineGap($source->getLineGap());

        $advanceWidthMax = 0;
        $minLeftSideBearing = PHP_INT_MAX;
        $minRightSideBearing = PHP_INT_MAX;
        $xMaxExtent = 0;
        foreach ($characters as $character) {
            $advanceWidth = $character->getLongHorMetric()->getAdvanceWidth();
            $leftSideBearing = $character->getLongHorMetric()->getLeftSideBearing();

            $advanceWidthMax = max($advanceWidthMax, $advanceWidth);
            $minLeftSideBearing = min($minLeftSideBearing, $leftSideBearing);

            $width = $character->getBoundingBox()->getWidth();
            $rightSideBearing = $advanceWidth - $leftSideBearing - $width;
            $minRightSideBearing = min($minRightSideBearing, $rightSideBearing);

            $xExtend = $leftSideBearing + $width;
            $xMaxExtent = max($xMaxExtent, $xExtend);
        }

        $hHeaTable->setAdvanceWidthMax($advanceWidthMax);
        $hHeaTable->setMinLeftSideBearing($minLeftSideBearing);
        $hHeaTable->setMinRightSideBearing($minRightSideBearing);
        $hHeaTable->setXMaxExtent($xMaxExtent);

        $hHeaTable->setCaretSlopeRise($source->getCaretSlopeRise());
        $hHeaTable->setCaretSlopeRun($source->getCaretSlopeRun());
        $hHeaTable->setCaretOffset($source->getCaretOffset());

        $hHeaTable->setMetricDataFormat(0);
        $hHeaTable->setNumOfLongHorMetrics($longHorMetricCount);

        return $hHeaTable;
    }

    /**
     * @param Character[] $characters
     *
     * @return HMtxTable
     */
    private function generateHMtxTable(array $characters)
    {
        $hmtx = new HMtxTable();

        foreach ($characters as $character) {
            $longHorMetric = new LongHorMetric();
            $longHorMetric->setAdvanceWidth($character->getLongHorMetric()->getAdvanceWidth());
            $longHorMetric->setLeftSideBearing($character->getLongHorMetric()->getLeftSideBearing());

            $hmtx->addLongHorMetric($longHorMetric);
        }

        return $hmtx;
    }

    /**
     * @param \PdfGenerator\Font\Frontend\File\Table\MaxPTable $source
     * @param Character[] $characters
     *
     * @return MaxPTable
     */
    private function generateMaxPTable(\PdfGenerator\Font\Frontend\File\Table\MaxPTable $source, array $characters)
    {
        $maxPTable = new MaxPTable();

        $maxPTable->setVersion(1.0);
        $maxPTable->setNumGlyphs(\count($characters));
        $maxPTable->setMaxPoints($source->getMaxPoints());
        $maxPTable->setMaxContours($source->getMaxContours());
        $maxPTable->setMaxCompositePoints($source->getMaxCompositePoints());
        $maxPTable->setMaxCompositeContours($source->getMaxCompositeContours());
        $maxPTable->setMaxZones($source->getMaxZones());
        $maxPTable->setMaxTwilightPoints($source->getMaxTwilightPoints());
        $maxPTable->setMaxStorage($source->getMaxStorage());
        $maxPTable->setMaxFunctionDefs($source->getMaxFunctionDefs());
        $maxPTable->setMaxInstructionDefs($source->getMaxInstructionDefs());
        $maxPTable->setMaxStackElements($source->getMaxStackElements());
        $maxPTable->setMaxSizeOfInstructions($source->getMaxSizeOfInstructions());
        $maxPTable->setMaxComponentElements($source->getMaxComponentElements());
        $maxPTable->setMaxComponentDepth($source->getMaxComponentDepth());

        return $maxPTable;
    }

    /**
     * @param array $characters
     *
     * @return Subtable
     */
    private function generateSubtable(array $characters): Subtable
    {
        $subtable = new Subtable();

        $subtable->setPlatformID(3);
        $subtable->setPlatformSpecificID(4);

        $subtable->setFormat($this->generateCMapFormat4($characters));

        return $subtable;
    }

    /**
     * @param Character[] $characters
     *
     * @return Format4
     */
    private function generateCMapFormat4(array $characters)
    {
        $segments = $this->generateSegments($characters);
        $segmentsCount = \count($segments);

        $format = new Format4();
        $format->setLength(8 * 2 + 4 * 2 * $segmentsCount); // 8 fields; 4 arrays of size 2 per entry
        $format->setLanguage(0);
        $format->setSegCountX2($segmentsCount * 2);
        self::setBinaryTreeSearchableProperties($format, $format->getSegCountX2());
        $format->setEntrySelector($format->getEntrySelector() - 1);
        $format->setReservedPad(0);

        foreach ($segments as $segment) {
            $format->addStartCode($segment->getStartCode());
            $format->addEndCode($segment->getEndCode());
            $format->addIdDelta($segment->getIdDelta());
            $format->addIdRangeOffset($segment->getIdRangeOffset());
        }

        return $format;
    }

    /**
     * @param Character[] $characters
     *
     * @return Segment[]
     */
    private function generateSegments(array $characters): array
    {
        /** @var Segment[] $segments */
        $segments = [];

        $lastUnicodePoint = -1;
        /** @var Segment $currentSegment */
        $currentSegment = null;
        $characterCount = \count($characters);

        // start with index 1 because 0 is the missing glyph character
        for ($i = 1; $i < $characterCount; ++$i) {
            $character = $characters[$i];
            if ($character->getUnicodePoint() + 1 === $lastUnicodePoint) {
                $currentSegment->setEndCode($character->getUnicodePoint());
                // reuse current segment
                continue;
            }

            if ($currentSegment !== null) {
                $segments[] = $currentSegment;
            }

            $currentSegment = new Segment();
            $currentSegment->setStartCode($character->getUnicodePoint());
            $currentSegment->setEndCode($character->getUnicodePoint());
            $currentSegment->setIdRangeOffset(0);
            $currentSegment->setIdDelta($i - $character->getUnicodePoint());
        }

        $segments[] = $currentSegment;

        $endSegment = new Segment();
        $endSegment->setStartCode(0xFFFF);
        $endSegment->setEndCode(0xFFFF);
        $endSegment->setIdDelta(1);
        $endSegment->setIdRangeOffset(0);

        $segments[] = $endSegment;

        return $segments;
    }

    /**
     * @param array $characters
     *
     * @return array
     */
    private function sortCharactersByCodePoint(array $characters): array
    {
        $charactersByCodePoint = [];
        foreach ($characters as $character) {
            $charactersByCodePoint[$character->getUnicodePoint()] = $character;
        }

        ksort($charactersByCodePoint);

        return array_values($charactersByCodePoint);
    }

    /**
     * @param Character[] $characters
     *
     * @return GlyfTable[]
     */
    private function generateGlyfTables(array $characters)
    {
        /** @var GlyfTable[] $glyfTables */
        $glyfTables = [];

        foreach ($characters as $character) {
            $glyfTables[] = $this->generateGlyfTable($character->getGlyfTable());
        }

        return $glyfTables;
    }

    /**
     * @param GlyfTable[] $glyfTables
     *
     * @return LocaTable
     */
    private function generateLocaTable(array $glyfTables)
    {
        $locaTable = new LocaTable();

        // offset with words
        $currentOffset = 0;

        $locaTable->addOffset($currentOffset);
        foreach ($glyfTables as $glyfTable) {
            $size = \strlen($glyfTable->getContent()) + 10;

            $currentOffset += $size / 2;
            $locaTable->addOffset($currentOffset);
        }

        return $locaTable;
    }

    /**
     * @param TableDirectory $fontFile
     *
     * @return string
     * @throws \Exception
     *
     */
    private function writeTableDirectory(TableDirectory $fontFile)
    {
        /** @var BaseTable[] $tables */
        $tables = [
            'cmap' => $fontFile->getCMapTable(),
            'head' => $fontFile->getHeadTable(),
            'hhea' => $fontFile->getHHeaTable(),
            'hmtx' => $fontFile->getHMtxTable(),
            'loca' => $fontFile->getLocaTable(),
            'maxp' => $fontFile->getMaxPTable(),
            'name' => $fontFile->getNameTable(),
            'post' => $fontFile->getPostTable(),
            'glyf' => $fontFile->getGlyphTables(),
        ];

        foreach ($fontFile->getRawTables() as $table) {
            $tables[$table->getTag()] = $table;
        }

        ksort($tables);

        $tableStreamWriter = new StreamWriter();

        $offsetByTag = [];
        foreach ($tables as $tag => $table) {
            if ($table === null) {
                continue;
            }

            $offsetByTag[$tag] = $tableStreamWriter->getLength();
            if (\is_array($table)) {
                foreach ($table as $item) {
                    /* @var BaseTable $item */
                    $tableStreamWriter->writeStream($item->accept($this->tableVisitor));
                }
            } else {
                $tableStreamWriter->writeStream($table->accept($this->tableVisitor));
            }
        }

        $tableDirectoryEntries = $this->generateTableDirectoryEntries($offsetByTag, $tableStreamWriter->getLength());
        $offsetTable = $this->generateOffsetTable(\count($tableDirectoryEntries));

        $streamWriter = new StreamWriter();
        $streamWriter->writeStream($offsetTable->accept($this->tableVisitor));

        foreach ($tableDirectoryEntries as $tableDirectoryEntry) {
            $streamWriter->writeStream($tableDirectoryEntry->accept($this->tableVisitor));
        }

        $streamWriter->writeStream($tableStreamWriter->getStream());

        return $streamWriter->getStream();
    }

    /**
     * @param int $numTables
     *
     * @return OffsetTable
     */
    private function generateOffsetTable(int $numTables)
    {
        $offsetTable = new OffsetTable();

        $offsetTable->setScalerType(0x00010000);
        $offsetTable->setNumTables($numTables);
        self::setBinaryTreeSearchableProperties($offsetTable, $numTables);

        return $offsetTable;
    }

    /**
     * @param array $offsetByTag
     * @param int $totalStreamLength
     *
     * @return TableDirectoryEntry[]
     */
    private function generateTableDirectoryEntries(array $offsetByTag, int $totalStreamLength): array
    {
        /** @var TableDirectoryEntry[] $tableDirectoryEntries */
        $tableDirectoryEntries = [];
        foreach ($offsetByTag as $tag => $offset) {
            $tableDirectoryEntry = new TableDirectoryEntry();
            $tableDirectoryEntry->setTag($tag);
            $tableDirectoryEntry->setOffset($offset);
            $tableDirectoryEntry->setCheckSum(0);

            $tableDirectoryEntries[] = $tableDirectoryEntry;
        }

        // calculate length
        for ($i = 0; $i < \count($tableDirectoryEntries); ++$i) {
            if ($i + 1 === \count($tableDirectoryEntries)) {
                $nextOffset = $totalStreamLength;
            } else {
                $nextOffset = $tableDirectoryEntries[$i + 1]->getOffset();
            }

            $currentEntry = $tableDirectoryEntries[$i];
            $currentEntry->setLength($nextOffset - $currentEntry->getOffset());
        }

        // adjust offset
        $numTables = \count($offsetByTag);
        $prefixOverhead = $numTables * 16 + 12;
        foreach ($tableDirectoryEntries as $tableDirectoryEntry) {
            $tableDirectoryEntry->setOffset($tableDirectoryEntry->getOffset() + $prefixOverhead);
        }

        return $tableDirectoryEntries;
    }

    /**
     * @param \PdfGenerator\Font\Frontend\File\Table\PostTable $source
     * @param Character[] $characters
     *
     * @return PostTable
     */
    private function generatePostTable(\PdfGenerator\Font\Frontend\File\Table\PostTable $source, array $characters)
    {
        $postTable = new PostTable();

        $postTable->setVersion(2.0);
        $postTable->setItalicAngle($source->getItalicAngle());
        $postTable->setUnderlinePosition($source->getUnderlinePosition());
        $postTable->setUnderlineThickness($source->getUnderlineThickness());
        $postTable->setIsFixedPitch($source->getIsFixedPitch());
        $postTable->setMinMemType42($source->getMinMemType42());
        $postTable->setMaxMemType42($source->getMaxMemType42());
        $postTable->setMinMemType1($source->getMinMemType1());
        $postTable->setMaxMemType1($source->getMaxMemType1());

        $postTable->setFormat($this->generatePostFormat2Table($characters));

        return $postTable;
    }

    /**
     * @param \PdfGenerator\Font\Frontend\File\Table\NameTable $source
     * @return NameTable
     */
    private function generateNameTable(\PdfGenerator\Font\Frontend\File\Table\NameTable $source)
    {
        $nameTable = new NameTable();

        // use version 0; hence no lang tag records
        $nameTable->setFormat(0);
        $nameRecordCount = count($source->getNameRecords());
        $nameTable->setCount($nameRecordCount);

        $sizeOfNameRecords = $nameRecordCount * 12;
        $stringOffset = 6 + $sizeOfNameRecords;
        $nameTable->setStringOffset($stringOffset);

        $valueOffset = 0;
        foreach ($source->getNameRecords() as $nameRecordSource) {
            $nameRecord = new NameRecord();
            $nameRecord->setPlatformID($nameRecordSource->getPlatformID());
            $nameRecord->setEncodingID($nameRecordSource->getEncodingID());
            $nameRecord->setLanguageID($nameRecordSource->getLanguageID());
            $nameRecord->setNameID($nameRecordSource->getNameID());

            $value = $nameRecordSource->getValue();
            $nameRecord->setValue($value);

            $valueLength = strlen($value);
            $nameRecord->setLength($valueLength);

            $nameRecord->setOffset($valueOffset);
            $valueOffset += $valueLength;
        }

        return $nameTable;
    }

    /**
     * @param Character[] $characters
     *
     * @return CMapTable
     */
    private function generateCMapTable(array $characters): CMapTable
    {
        $cMapTable = new CMapTable();

        $cMapTable->setVersion(0);
        $cMapTable->setNumberSubtables(1);

        $cMapTable->addSubtable($this->generateSubtable($characters));

        return $cMapTable;
    }

    /**
     * @param Character[] $characters
     *
     * @return Format2
     */
    private function generatePostFormat2Table(array $characters)
    {
        $format2 = new Format2();

        $format2->setNumGlyphs(\count($characters));

        $names = [];
        foreach ($characters as $character) {
            $postScriptInfo = $character->getPostScriptInfo();
            if ($postScriptInfo->isInStandardMacintoshSet()) {
                $format2->addGlyphNameIndex($postScriptInfo->getMacintoshGlyphIndex());
            } else {
                $nameIndex = 258 + \count($names);
                $format2->addGlyphNameIndex($nameIndex);
                $names[] = $postScriptInfo->getName();
            }
        }

        $namePascalString = self::generatePascalString($names);
        $format2->setNames($namePascalString);

        return $format2;
    }

    /**
     * @param \PdfGenerator\Font\Frontend\File\Table\RawTable $table
     *
     * @return RawTable
     */
    private function generateRawTable(\PdfGenerator\Font\Frontend\File\Table\RawTable $table)
    {
        $rawTable = new RawTable();

        $rawTable->setTag($table->getTag());
        $rawTable->setContent($table->getContent());

        return $rawTable;
    }

    /**
     * @param string[] $names
     *
     * @return string
     */
    private static function generatePascalString(array $names): string
    {
        $nameString = '';
        foreach ($names as $name) {
            $nameString .= \strlen($name);
            $nameString[] = $name;
        }

        return $nameString;
    }

    /**
     * @param BinaryTreeSearchableTrait $binaryTreeSearchable
     * @param int $numberOfEntries
     */
    private static function setBinaryTreeSearchableProperties($binaryTreeSearchable, int $numberOfEntries)
    {
        $powerOfTwo = (int)log($numberOfEntries, 2);

        $binaryTreeSearchable->setSearchRange(pow(2, $powerOfTwo));
        $binaryTreeSearchable->setEntrySelector($powerOfTwo);
        $binaryTreeSearchable->setRangeShift($numberOfEntries - $binaryTreeSearchable->getSearchRange());
    }

    /**
     * @param \PdfGenerator\Font\IR\Structure\TableDirectory $tableDirectory
     *
     * @return RawTable[]
     */
    private function generateRawTables(\PdfGenerator\Font\IR\Structure\TableDirectory $tableDirectory): array
    {
        $tables = array_merge([
            $tableDirectory->getCvtTable(),
            $tableDirectory->getFpgmTable(),
            $tableDirectory->getGaspTable(),
            $tableDirectory->getGDEFTable(),
            $tableDirectory->getGPOSTable(),
            $tableDirectory->getGSUBTable(),
            $tableDirectory->getOS2Table(),
            $tableDirectory->getPrepTable(),
        ], $tableDirectory->getRawTables());

        $rawTables = [];
        foreach ($tables as $table) {
            if ($table !== null) {
                $rawTables[] = $this->generateRawTable($table);
            }
        }

        return $rawTables;
    }

    /**
     * @param Font $font
     *
     * @return TableDirectory
     */
    private function createTableDirectory(Font $font): TableDirectory
    {
        $characters = $this->prepareCharacters($font->getCharacters(), $font->getMissingGlyphCharacter());

        $tableDirectory = new TableDirectory();
        $tableDirectory->setCMapTable($this->generateCMapTable($characters));
        $tableDirectory->setHMtxTable($this->generateHMtxTable($characters));
        $tableDirectory->setHeadTable($this->generateHeadTable($font->getTableDirectory()->getHeadTable(), $characters));
        $tableDirectory->setPostTable($this->generatePostTable($font->getTableDirectory()->getPostTable(), $characters));
        $tableDirectory->setMaxPTable($this->generateMaxPTable($font->getTableDirectory()->getMaxPTable(), $characters));
        $tableDirectory->setNameTable($this->generateNameTable($font->getTableDirectory()->getNameTable()));

        $tableDirectory->setGlyphTables($this->generateGlyfTables($characters));
        $tableDirectory->setLocaTable($this->generateLocaTable($tableDirectory->getGlyphTables()));

        $longHorMetricCount = \count($tableDirectory->getHMtxTable()->getLongHorMetrics());
        $tableDirectory->setHHeaTable($this->generateHHeaTable($font->getTableDirectory()->getHHeaTable(), $characters, $longHorMetricCount));

        $tableDirectory->setRawTables($this->generateRawTables($font->getTableDirectory()));

        return $tableDirectory;
    }

    /**
     * @param \PdfGenerator\Font\Frontend\File\Table\GlyfTable $source
     *
     * @return GlyfTable
     */
    private function generateGlyfTable(\PdfGenerator\Font\Frontend\File\Table\GlyfTable $source): GlyfTable
    {
        $glyfTable = new GlyfTable();

        $glyfTable->setNumberOfContours($source->getNumberOfContours());
        $glyfTable->setXMin($source->getXMin());
        $glyfTable->setXMax($source->getXMax());
        $glyfTable->setYMin($source->getYMin());
        $glyfTable->setYMax($source->getYMax());
        $glyfTable->setContent($source->getContent());

        return $glyfTable;
    }
}
