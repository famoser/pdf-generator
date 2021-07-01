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
use PdfGenerator\Font\Backend\File\Table\Glyf\ComponentGlyf;
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
use PdfGenerator\Font\Backend\File\Table\OS2Table;
use PdfGenerator\Font\Backend\File\Table\Post\Format\Format2;
use PdfGenerator\Font\Backend\File\Table\PostTable;
use PdfGenerator\Font\Backend\File\Table\RawTable;
use PdfGenerator\Font\Backend\File\Table\TableDirectoryEntry;
use PdfGenerator\Font\Backend\File\TableDirectory;
use PdfGenerator\Font\Backend\File\TableVisitor;
use PdfGenerator\Font\Backend\File\Traits\BinaryTreeSearchableTrait;
use PdfGenerator\Font\Frontend\StreamReader;
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
     * @throws \Exception
     *
     * @return string
     */
    public function writeFont(Font $font)
    {
        $tableDirectory = $this->createTableDirectory($font);

        return $this->writeTableDirectory($tableDirectory);
    }

    /**
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

        $xMin = \PHP_INT_MAX;
        $yMin = \PHP_INT_MAX;
        $xMax = 0;
        $yMax = 0;
        foreach ($characters as $character) {
            if ($character->getGlyfTable() === null || $character->getGlyfTable()->getNumberOfContours() === 0) {
                continue;
            }

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
     * @param Character[] $characters
     *
     * @return HHeaTable
     */
    private function generateHHeaTable(\PdfGenerator\Font\Frontend\File\Table\HHeaTable $source, array $characters, int $longHorMetricCount)
    {
        $hHeaTable = new HHeaTable();

        $hHeaTable->setVersion(1.0);
        $hHeaTable->setAscent($source->getAscent());
        $hHeaTable->setDescent($source->getDescent());
        $hHeaTable->setLineGap($source->getLineGap());

        $advanceWidthMax = 0;
        $minLeftSideBearing = \PHP_INT_MAX;
        $minRightSideBearing = \PHP_INT_MAX;
        $xMaxExtent = 0;
        foreach ($characters as $character) {
            $advanceWidth = $character->getLongHorMetric()->getAdvanceWidth();
            $advanceWidthMax = max($advanceWidthMax, $advanceWidth);

            // minRightSidebearing, minLeftSideBearing and xMaxExtent should be computed using only glyphs that have contours
            if ($character->getGlyfTable() === null || $character->getGlyfTable()->getNumberOfContours() === 0) {
                continue;
            }

            $leftSideBearing = $character->getLongHorMetric()->getLeftSideBearing();
            $minLeftSideBearing = min($minLeftSideBearing, $leftSideBearing);

            if ($character->getGlyfTable() !== null) {
                $width = $character->getGlyfTable()->getXMax() - $character->getGlyfTable()->getXMin();
                $rightSideBearing = $advanceWidth - $leftSideBearing - $width;
                $minRightSideBearing = min($minRightSideBearing, $rightSideBearing);

                $xExtend = $leftSideBearing + $width;
                $xMaxExtent = max($xMaxExtent, $xExtend);
            }
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
     * @param Character[] $characters
     *
     * @return MaxPTable
     */
    private function generateMaxPTable(\PdfGenerator\Font\Frontend\File\Table\MaxPTable $source, array $characters)
    {
        $maxPTable = new MaxPTable();

        /*
         * some of the values here are wrong because we do not analyse the content of the glyphs
         * we leave the value same than they were with the input font; assuming the font gets less complex
         * we riks using too much memory for parses that trust these numbers, but they should not crash (because not enough memory allocated)
         */

        $maxPTable->setVersion(1.0);
        $maxPTable->setNumGlyphs(\count($characters));
        $maxPTable->setMaxPoints($source->getMaxPoints());

        $maxContours = 0;
        foreach ($characters as $character) {
            if ($character->getGlyfTable() === null || $character->getGlyfTable()->getNumberOfContours() === 0) {
                continue;
            }

            $maxContours = max($maxContours, $character->getGlyfTable()->getNumberOfContours());
        }

        $maxPTable->setMaxContours($maxContours);
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

    private function generateSubtable(array $characters, int $reservedCharactersOffset): Subtable
    {
        $subtable = new Subtable();

        $subtable->setPlatformID(0);
        $subtable->setPlatformSpecificID(4);

        $subtable->setFormat($this->generateCMapFormat4($characters, $reservedCharactersOffset));

        return $subtable;
    }

    /**
     * @param Character[] $characters
     *
     * @return Format4
     */
    private function generateCMapFormat4(array $characters, int $reservedCharactersOffset)
    {
        $segments = $this->generateSegments($characters, $reservedCharactersOffset);
        $segmentsCount = \count($segments);

        $format = new Format4();
        $format->setLength(8 * 2 + 4 * 2 * $segmentsCount); // 8 fields; 4 arrays of size 2 per entry
        $format->setLanguage(0);
        $format->setSegCountX2($segmentsCount * 2);
        $format->setSearchRange(2 * (2 ** ((int)(log($segmentsCount, 2)))));
        $format->setEntrySelector((int)log($format->getSearchRange() / 2, 2));
        $format->setRangeShift(2 * $segmentsCount - $format->getSearchRange());
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
    private function generateSegments(array $characters, int $reservedCharactersOffset): array
    {
        /** @var Segment[] $segments */
        $segments = [];

        $lastUnicodePoint = -1;
        /** @var Segment $currentSegment */
        $currentSegment = null;
        $characterCount = \count($characters);

        for ($i = 0; $i < $characterCount; ++$i) {
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
            $currentSegment->setIdDelta($reservedCharactersOffset + $i - $character->getUnicodePoint());
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
            if ($character->getGlyfTable() === null) {
                $glyfTables[] = null;
            } else {
                $glyfTables[] = $this->generateGlyfTable($character->getGlyfTable());
            }
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

        // TODO: refactor to calculate on level below? code here serves no benefit
        foreach ($glyfTables as $glyfTable) {
            if ($glyfTable !== null) {
                $size = 2 + 8; // contours + bounding box
                if ($glyfTable->getContent()) {
                    $size += \strlen($glyfTable->getContent());
                }

                foreach ($glyfTable->getComponentGlyphs() as $componentGlyph) {
                    $size += 4;
                    if ($componentGlyph->getContent()) {
                        $size += \strlen($componentGlyph->getContent());
                    }
                }

                $currentOffset += $size / 2;
            }

            $locaTable->addOffset($currentOffset);
        }

        return $locaTable;
    }

    /**
     * @throws \Exception
     *
     * @return string
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
            'OS/2' => $fontFile->getOS2Table(),
            'glyf' => $fontFile->getGlyphTables(),
        ];

        foreach ($fontFile->getRawTables() as $table) {
            $tables[$table->getTag()] = $table;
        }

        ksort($tables);

        $tableStreamWriter = new StreamWriter();

        /** @var TableDirectoryEntry[] $tableDirectoryEntries */
        $tableDirectoryEntries = [];

        foreach ($tables as $tag => $table) {
            if ($table === null) {
                continue;
            }

            $tableDirectoryEntry = new TableDirectoryEntry();
            $tableDirectoryEntry->setTag($tag);
            $tableDirectoryEntry->setOffset($tableStreamWriter->getLength());

            $stream = '';
            if (\is_array($table)) {
                foreach ($table as $item) {
                    // glyph tables can be null if they have no content
                    if ($item === null) {
                        continue;
                    }

                    /* @var BaseTable $item */
                    $stream .= $item->accept($this->tableVisitor);
                }
            } else {
                $stream .= $table->accept($this->tableVisitor);
            }

            $tableDirectoryEntry->setCheckSum($this->calculateCheckum($stream));

            $tableStreamWriter->writeStream($stream);
            $tableDirectoryEntry->setLength($tableStreamWriter->getLength() - $tableDirectoryEntry->getOffset());
            $tableStreamWriter->byteAlign(4);

            $tableDirectoryEntries[] = $tableDirectoryEntry;
        }

        // adjust offset
        $numTables = \count($tableDirectoryEntries);
        $prefixOverhead = $numTables * 16 + 12;
        foreach ($tableDirectoryEntries as $tableDirectoryEntry) {
            $tableDirectoryEntry->setOffset($tableDirectoryEntry->getOffset() + $prefixOverhead);
        }

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
     * @return int
     */
    private function calculateCheckum(string $stream)
    {
        $length = \strlen($stream);

        $reader = new StreamReader($stream);
        $upperSum = 0;
        $lowerSum = 0;
        while ($length >= 4) {
            $upperSum += $reader->readUInt16();
            $lowerSum += $reader->readUInt16();

            $length -= 4;
        }

        if (!$reader->isEndOfFileReached()) {
            $upperSum += $reader->readUInt8() << 8;
        }

        if (!$reader->isEndOfFileReached()) {
            $upperSum += $reader->readUInt8();
        }

        if (!$reader->isEndOfFileReached()) {
            $lowerSum += $reader->readUInt8() << 8;
        }

        $upperSum += $lowerSum >> 16;

        $upperNumber = ($upperSum << 16) & 0xFFFF0000;
        $lowerNumber = $lowerSum & 0xFFFF;

        return $upperNumber | $lowerNumber;
    }

    /**
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
     * @return NameTable
     */
    private function generateNameTable(\PdfGenerator\Font\Frontend\File\Table\NameTable $source)
    {
        $nameTable = new NameTable();

        // use version 0; hence no lang tag records
        $nameTable->setFormat(0);
        $nameRecordCount = \count($source->getNameRecords());
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

            $valueLength = \strlen($value);
            $nameRecord->setLength($valueLength);

            $nameRecord->setOffset($valueOffset);
            $valueOffset += $valueLength;

            $nameTable->addNameRecord($nameRecord);
        }

        return $nameTable;
    }

    /**
     * @param Character[] $characters
     */
    private function generateCMapTable(array $characters, int $reservedCharactersOffset): CMapTable
    {
        $cMapTable = new CMapTable();

        $cMapTable->setVersion(0);
        $cMapTable->setNumberSubtables(1);

        $cMapTable->addSubtable($this->generateSubtable($characters, $reservedCharactersOffset));

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
     */
    private static function generatePascalString(array $names): string
    {
        $writer = new StreamWriter();

        foreach ($names as $name) {
            $writer->writeUInt8(\strlen($name));
            $writer->writeStream($name);
        }

        return $writer->getStream();
    }

    /**
     * @param BinaryTreeSearchableTrait $binaryTreeSearchable
     */
    private static function setBinaryTreeSearchableProperties($binaryTreeSearchable, int $numberOfEntries)
    {
        $powerOfTwo = (int)log($numberOfEntries, 2);

        $binaryTreeSearchable->setSearchRange(2 ** $powerOfTwo * 16);
        $binaryTreeSearchable->setEntrySelector($powerOfTwo);
        $binaryTreeSearchable->setRangeShift($numberOfEntries * 16 - $binaryTreeSearchable->getSearchRange());
    }

    /**
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

    private function createTableDirectory(Font $font): TableDirectory
    {
        $characters = $this->sortCharactersByCodePoint($font->getCharacters());
        $reservedCharactersOffset = \count($font->getReservedCharacters());

        $tableDirectory = new TableDirectory();
        $tableDirectory->setCMapTable($this->generateCMapTable($characters, $reservedCharactersOffset));

        array_unshift($characters, ...$font->getReservedCharacters());
        $tableDirectory->setHMtxTable($this->generateHMtxTable($characters));
        $tableDirectory->setHeadTable($this->generateHeadTable($font->getTableDirectory()->getHeadTable(), $characters));
        $tableDirectory->setPostTable($this->generatePostTable($font->getTableDirectory()->getPostTable(), $characters));
        $tableDirectory->setMaxPTable($this->generateMaxPTable($font->getTableDirectory()->getMaxPTable(), $characters));
        $tableDirectory->setNameTable($this->generateNameTable($font->getTableDirectory()->getNameTable()));
        $tableDirectory->setOS2Table($this->generateOS2Table($font->getTableDirectory()->getOS2Table(), $characters));

        $tableDirectory->setGlyphTables($this->generateGlyfTables($characters));
        $tableDirectory->setLocaTable($this->generateLocaTable($tableDirectory->getGlyphTables()));

        $longHorMetricCount = \count($tableDirectory->getHMtxTable()->getLongHorMetrics());
        $tableDirectory->setHHeaTable($this->generateHHeaTable($font->getTableDirectory()->getHHeaTable(), $characters, $longHorMetricCount));

        $tableDirectory->setRawTables($this->generateRawTables($font->getTableDirectory()));

        return $tableDirectory;
    }

    private function generateGlyfTable(\PdfGenerator\Font\Frontend\File\Table\GlyfTable $source): GlyfTable
    {
        $glyfTable = new GlyfTable();

        $glyfTable->setNumberOfContours($source->getNumberOfContours());
        $glyfTable->setXMin($source->getXMin());
        $glyfTable->setXMax($source->getXMax());
        $glyfTable->setYMin($source->getYMin());
        $glyfTable->setYMax($source->getYMax());

        foreach ($source->getComponentGlyphs() as $componentGlyph) {
            $backendComponentGlyph = new ComponentGlyf();
            $backendComponentGlyph->setFlags($componentGlyph->getFlags());
            // TODO: convert component glyph references to component indexes
            $backendComponentGlyph->setGlyphIndex($componentGlyph->getGlyphIndex());
            $backendComponentGlyph->setContent($componentGlyph->getContent());

            $glyfTable->addComponentGlyph($backendComponentGlyph);
        }

        $glyfTable->setContent($source->getContent());

        return $glyfTable;
    }

    /**
     * @param Character[] $characters
     *
     * @return OS2Table
     */
    private function generateOS2Table(\PdfGenerator\Font\Frontend\File\Table\OS2Table $source, array $characters)
    {
        $os2Table = new OS2Table();

        $os2Table->setVersion(5);

        $totalWidth = 0;
        $minUnicode = 0xFFFF;
        $maxUnicode = 0;
        $characterWithNonZeroWidth = 0;
        foreach ($characters as $character) {
            $totalWidth += $character->getLongHorMetric()->getAdvanceWidth();

            if ($character->getLongHorMetric()->getAdvanceWidth() > 0) {
                ++$characterWithNonZeroWidth;
            }

            if ($character->getUnicodePoint() > 0) {
                $minUnicode = min($minUnicode, $character->getUnicodePoint());
                $maxUnicode = max($maxUnicode, $character->getUnicodePoint());
            }
        }
        $maxUnicode = min($maxUnicode, 0xFFFF);
        $os2Table->setXAvgCharWidth($totalWidth / $characterWithNonZeroWidth);

        $os2Table->setUsWeightClass($source->getUsWeightClass());
        $os2Table->setUsWidthClass($source->getUsWidthClass());

        $os2Table->setFsType($source->getFsType());
        $os2Table->setYSubscriptXSize($source->getYSubscriptXSize());
        $os2Table->setYSubscriptYSize($source->getYSubscriptYSize());
        $os2Table->setYSubscriptXOffset($source->getYSubscriptXOffset());
        $os2Table->setYSubscriptYOffset($source->getYSubscriptYOffset());

        $os2Table->setFsType($source->getFsType());
        $os2Table->setYSuperscriptXSize($source->getYSuperscriptXSize());
        $os2Table->setYSuperscriptYSize($source->getYSuperscriptYSize());
        $os2Table->setYSuperscriptXOffset($source->getYSuperscriptXOffset());
        $os2Table->setYSuperscriptYOffset($source->getYSuperscriptYOffset());

        $os2Table->setYStrikeoutSize($source->getYStrikeoutSize());
        $os2Table->setYStrikeoutPosition($source->getYStrikeoutPosition());

        $os2Table->setSFamilyClass($source->getSFamilyClass());
        $os2Table->setPanose($source->getPanose());

        $os2Table->setUlUnicodeRanges($source->getUlUnicodeRanges());
        $os2Table->setUlUnicodeRanges([0, 0, 0, 0]);

        $os2Table->setAchVendID($source->getAchVendID());
        $os2Table->setFsSelection($source->getFsSelection());

        $os2Table->setUsFirstCharIndex($minUnicode);
        $os2Table->setUsLastCharIndex($maxUnicode);

        $os2Table->setSTypoAscender($source->getSTypoAscender());
        $os2Table->setSTypoDecender($source->getSTypoDecender());
        $os2Table->setSTypoLineGap($source->getSTypoLineGap());

        $os2Table->setUsWinAscent($source->getUsWinAscent());
        $os2Table->setUsWinDecent($source->getUsWinDecent());

        if ($source->getVersion() > 0) {
            $os2Table->setUlCodePageRanges($source->getUlCodePageRanges());
        } else {
            $os2Table->setUlCodePageRanges([0, 0]);
        }
        $os2Table->setUlCodePageRanges([0, 0]);

        if ($source->getVersion() > 3) {
            $os2Table->setSxHeight($source->getSxHeight());
            $os2Table->setSCapHeight($source->getSCapHeight());

            $os2Table->setUsDefaultChar($source->getUsDefaultChar());
            $os2Table->setUsBreakChar($source->getUsBreakChar());
            $os2Table->setUsMaxContext($source->getUsMaxContext());
        } else {
            $os2Table->setSxHeight(0); // should be height of lowercase x character
            $os2Table->setSCapHeight(0); // should be equal the height of uppercase H character

            $os2Table->setUsDefaultChar(0); // use glyph 0
            $os2Table->setUsBreakChar(32); // use space, unicode code point 32
            $os2Table->setUsMaxContext(3); // would need to check for ligatures & the like; now we just assume 3 which should be enough
        }

        if ($source->getVersion() > 4) {
            $os2Table->setUsLowerOptimalPointSize($source->getUsLowerOptimalPointSize());
            $os2Table->setUsUpperOptimalPointSize($source->getUsUpperOptimalPointSize());
        } else {
            $os2Table->setUsLowerOptimalPointSize(0);
            $os2Table->setUsUpperOptimalPointSize(0xFFFF);
        }

        return $os2Table;
    }
}
