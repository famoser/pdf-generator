<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Font\Backend;

use Famoser\PdfGenerator\Font\Backend\File\Table\Base\BaseTable;
use Famoser\PdfGenerator\Font\Backend\File\Table\CMap\Format\Format4;
use Famoser\PdfGenerator\Font\Backend\File\Table\CMap\Subtable;
use Famoser\PdfGenerator\Font\Backend\File\Table\CMapTable;
use Famoser\PdfGenerator\Font\Backend\File\Table\Glyf\ComponentGlyf;
use Famoser\PdfGenerator\Font\Backend\File\Table\GlyfTable;
use Famoser\PdfGenerator\Font\Backend\File\Table\HeadTable;
use Famoser\PdfGenerator\Font\Backend\File\Table\HHeaTable;
use Famoser\PdfGenerator\Font\Backend\File\Table\HMtx\LongHorMetric;
use Famoser\PdfGenerator\Font\Backend\File\Table\HMtxTable;
use Famoser\PdfGenerator\Font\Backend\File\Table\LocaTable;
use Famoser\PdfGenerator\Font\Backend\File\Table\MaxPTable;
use Famoser\PdfGenerator\Font\Backend\File\Table\Name\NameRecord;
use Famoser\PdfGenerator\Font\Backend\File\Table\NameTable;
use Famoser\PdfGenerator\Font\Backend\File\Table\OffsetTable;
use Famoser\PdfGenerator\Font\Backend\File\Table\OS2Table;
use Famoser\PdfGenerator\Font\Backend\File\Table\Post\Format\Format2;
use Famoser\PdfGenerator\Font\Backend\File\Table\PostTable;
use Famoser\PdfGenerator\Font\Backend\File\Table\RawTable;
use Famoser\PdfGenerator\Font\Backend\File\Table\TableDirectoryEntry;
use Famoser\PdfGenerator\Font\Backend\File\TableDirectory;
use Famoser\PdfGenerator\Font\Backend\File\TableVisitor;
use Famoser\PdfGenerator\Font\Frontend\StreamReader;
use Famoser\PdfGenerator\Font\IR\Structure\Character;
use Famoser\PdfGenerator\Font\IR\Structure\Font;
use Famoser\PdfGenerator\Font\IR\Utils\CMap\Format4\Segment;

readonly class FileWriter
{
    public function __construct(private TableVisitor $tableVisitor)
    {
    }

    public static function create(): FileWriter
    {
        $tableVisitor = TableVisitor::create();

        return new self($tableVisitor);
    }

    /**
     * @throws \Exception
     */
    public function writeFont(Font $font): string
    {
        if (!$font->getIsTrueTypeFont()) {
            throw new \Exception('Writing non-TrueType fonts is not supported at the moment.');
        }

        $tableDirectory = $this->createTableDirectory($font);

        return $this->writeTableDirectory($tableDirectory);
    }

    /**
     * @param Character[] $characters
     */
    private function generateHeadTable(\Famoser\PdfGenerator\Font\Frontend\File\Table\HeadTable $source, array $characters): HeadTable
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
            if (null === $character->getGlyfTable() || 0 === $character->getGlyfTable()->getNumberOfContours()) {
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
     */
    private function generateHHeaTable(\Famoser\PdfGenerator\Font\Frontend\File\Table\HHeaTable $source, array $characters, int $longHorMetricCount): HHeaTable
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
            if (null === $character->getGlyfTable() || 0 === $character->getGlyfTable()->getNumberOfContours()) {
                continue;
            }

            $leftSideBearing = $character->getLongHorMetric()->getLeftSideBearing();
            $minLeftSideBearing = min($minLeftSideBearing, $leftSideBearing);

            $width = $character->getGlyfTable()->getXMax() - $character->getGlyfTable()->getXMin();
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
     */
    private function generateHMtxTable(array $characters): HMtxTable
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
     */
    private function generateMaxPTable(\Famoser\PdfGenerator\Font\Frontend\File\Table\MaxPTable $source, array $characters): MaxPTable
    {
        $maxPTable = new MaxPTable();

        /*
         * some of the values here are wrong because we do not analyse the content of the glyphs
         * we leave the value same than they were with the input font; assuming the font gets less complex
         * we risk using too much memory for parses that trust these numbers, but the reverse could lead to crashes
         */

        $maxPTable->setVersion(1.0);
        $maxPTable->setNumGlyphs(\count($characters));
        $maxPTable->setMaxPoints($source->getMaxPoints());

        $maxContours = 0;
        foreach ($characters as $character) {
            if (null === $character->getGlyfTable() || 0 === $character->getGlyfTable()->getNumberOfContours()) {
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

    /**
     * @param Character[] $characters
     */
    private function generateSubtable(array $characters, int $reservedCharactersOffset): Subtable
    {
        $subtable = new Subtable();

        $subtable->setPlatformID(0);
        $subtable->setPlatformSpecificID(4);

        $format = $this->generateCMapFormat4($characters, $reservedCharactersOffset);
        $subtable->setFormat($format);

        return $subtable;
    }

    /**
     * @param Character[] $characters
     */
    private function generateCMapFormat4(array $characters, int $reservedCharactersOffset): Format4
    {
        $segments = $this->generateSegments($characters, $reservedCharactersOffset);
        $segmentsCount = \count($segments);

        $format = new Format4();
        $format->setLength(8 * 2 + 4 * 2 * $segmentsCount); // 8 fields; 4 arrays of size 2 per entry
        $format->setLanguage(0);
        $format->setSegCountX2($segmentsCount * 2);
        $format->setSearchRange(2 * (2 ** ((int)\log($segmentsCount, 2))));
        $format->setEntrySelector((int)\log($format->getSearchRange() / 2, 2));
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
        /** @var ?Segment $currentSegment */
        $currentSegment = null;
        $characterCount = \count($characters);

        for ($i = 0; $i < $characterCount; ++$i) {
            $character = $characters[$i];
            $characterUnicodePoint = $character->getUnicodePoint() ?? 0; // we do not expect a character here without a code point. but better to be clear about what happens then.

            if ($characterUnicodePoint + 1 === $lastUnicodePoint) {
                $currentSegment->setEndCode($characterUnicodePoint);
                // reuse current segment
                continue;
            }

            if (null !== $currentSegment) {
                $segments[] = $currentSegment;
            }

            $currentSegment = new Segment();
            $currentSegment->setStartCode($characterUnicodePoint);
            $currentSegment->setEndCode($characterUnicodePoint);
            $currentSegment->setIdRangeOffset(0);
            $currentSegment->setIdDelta($reservedCharactersOffset + $i - $characterUnicodePoint);
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
     * @param Character[] $characters
     *
     * @return (GlyfTable|null)[]
     */
    private function generateGlyfTables(array $characters): array
    {
        $glyfTables = [];

        foreach ($characters as $character) {
            if (null === $character->getGlyfTable()) {
                $glyfTables[] = null;
            } else {
                $glyfTables[] = $this->generateGlyfTable($character->getGlyfTable());
            }
        }

        return $glyfTables;
    }

    /**
     * @param (GlyfTable|null)[] $glyfTables
     */
    private function generateLocaTable(array $glyfTables): LocaTable
    {
        $locaTable = new LocaTable();

        // offset with words
        $currentOffset = 0;

        $locaTable->addOffset($currentOffset);

        // consider placing this in TableVisitor, as there glyf table sizes are known anyway
        // reduces code complexity
        foreach ($glyfTables as $glyfTable) {
            if (null !== $glyfTable) {
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
     */
    private function writeTableDirectory(TableDirectory $fontFile): string
    {
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
            $tableDirectoryEntry = new TableDirectoryEntry();
            $tableDirectoryEntry->setTag($tag);
            $tableDirectoryEntry->setOffset($tableStreamWriter->getLength());

            $stream = '';
            if (\is_array($table)) {
                foreach ($table as $item) {
                    // glyph tables can be null if they have no content
                    if (null === $item) {
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

    private function calculateCheckum(string $stream): int
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

    private function generateOffsetTable(int $numTables): OffsetTable
    {
        $offsetTable = new OffsetTable();

        $offsetTable->setScalerType(0x00010000);
        $offsetTable->setNumTables($numTables);

        // binary search properties
        $powerOfTwo = (int)\log($numTables, 2);
        $offsetTable->setSearchRange(2 ** $powerOfTwo * 16);
        $offsetTable->setEntrySelector($powerOfTwo);
        $offsetTable->setRangeShift($numTables * 16 - $offsetTable->getSearchRange());

        return $offsetTable;
    }

    /**
     * @param Character[] $characters
     */
    private function generatePostTable(\Famoser\PdfGenerator\Font\Frontend\File\Table\PostTable $source, array $characters): PostTable
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

    private function generateNameTable(\Famoser\PdfGenerator\Font\Frontend\File\Table\NameTable $source): NameTable
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
     */
    private function generatePostFormat2Table(array $characters): Format2
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

    private function generateRawTable(\Famoser\PdfGenerator\Font\Frontend\File\Table\RawTable $table): RawTable
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
     * @return RawTable[]
     */
    private function generateRawTables(\Famoser\PdfGenerator\Font\IR\Structure\TableDirectory $tableDirectory): array
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
            if (null !== $table) {
                $rawTables[] = $this->generateRawTable($table);
            }
        }

        return $rawTables;
    }

    private function createTableDirectory(Font $font): TableDirectory
    {
        $characters = $font->getCharacters();
        $reservedCharacters = $font->getReservedCharacters();

        $tableDirectory = new TableDirectory();
        $tableDirectory->setCMapTable($this->generateCMapTable($characters, \count($reservedCharacters)));

        array_unshift($characters, ...$reservedCharacters);
        $this->fixComponentCharacterReferences($characters);

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

    private function generateGlyfTable(\Famoser\PdfGenerator\Font\Frontend\File\Table\GlyfTable $source): GlyfTable
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
            $backendComponentGlyph->setGlyphIndex($componentGlyph->getGlyphIndex());
            $backendComponentGlyph->setContent($componentGlyph->getContent());

            $glyfTable->addComponentGlyph($backendComponentGlyph);
        }

        $glyfTable->setContent($source->getContent());

        return $glyfTable;
    }

    /**
     * @param Character[] $characters
     */
    private function generateOS2Table(\Famoser\PdfGenerator\Font\Frontend\File\Table\OS2Table $source, array $characters): OS2Table
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

    /**
     * @param Character[] $characters
     */
    private function fixComponentCharacterReferences(array $characters): void
    {
        $characterLookup = new \SplObjectStorage();
        $characterCount = \count($characters);
        for ($i = 0; $i < $characterCount; ++$i) {
            $characterLookup->attach($characters[$i], $i);
        }
        foreach ($characters as $character) {
            $componentCharacters = $character->getComponentCharacters();
            $componentCharacterCount = \count($componentCharacters);
            for ($i = 0; $i < $componentCharacterCount; ++$i) {
                $componentCharacter = $componentCharacters[$i];
                if (null !== $componentCharacter) {
                    // guaranteed to return result as all component characters part of font by @ref ensureComponentCharactersIncluded
                    $index = $characterLookup->offsetGet($componentCharacter);
                    $character->getGlyfTable()->getComponentGlyphs()[$i]->setGlyphIndex($index);
                }
            }
        }
    }
}
