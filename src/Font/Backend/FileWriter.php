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

use PdfGenerator\Font\Backend\File\FontFile;
use PdfGenerator\Font\Backend\File\Table\CMap\Format\Format4;
use PdfGenerator\Font\Backend\File\Table\CMap\Subtable;
use PdfGenerator\Font\Backend\File\Table\CMapTable;
use PdfGenerator\Font\Backend\File\Table\GlyfTable;
use PdfGenerator\Font\Backend\File\Table\HeadTable;
use PdfGenerator\Font\Backend\File\Table\HHeaTable;
use PdfGenerator\Font\Backend\File\Table\HMtxTable;
use PdfGenerator\Font\Backend\File\Table\LocaTable;
use PdfGenerator\Font\Backend\File\Table\MaxPTable;
use PdfGenerator\Font\Backend\File\Table\OffsetTable;
use PdfGenerator\Font\Backend\File\Table\Post\Format\Format2;
use PdfGenerator\Font\Backend\File\Table\PostTable;
use PdfGenerator\Font\Backend\File\Table\TableDirectoryEntry;
use PdfGenerator\Font\Backend\File\Traits\BinaryTreeSearchableTrait;
use PdfGenerator\Font\IR\Structure\Character;
use PdfGenerator\Font\IR\Structure\Font;
use PdfGenerator\Font\IR\Utils\CMap\Format4\Segment;

class FileWriter
{
    /**
     * @var TableWriter
     */
    private $tableWriter;

    /**
     * FileWriter constructor.
     *
     * @param TableWriter $tableWriter
     */
    public function __construct(TableWriter $tableWriter)
    {
        $this->tableWriter = $tableWriter;
    }

    /**
     * @param Font $font
     * @param Character[] $characters
     *
     * @return string
     * @throws \Exception
     *
     */
    public function writeFile(Font $font)
    {
        $characters = $this->prepareCharacters($font->getCharacters(), $font->getMissingGlyphCharacter());

        $fontFile = new FontFile();
        $fontFile->setCvtTable()
        $tables = [];
        $tables["hmtx"] = $this->generateHMtxTable($characters);
        $tables["cmap"] = $this->writeCMapTable($characters);
        $tables["glyf"] = $this->writeGlyfTables($characters);
        $tables["hmtx"] = $this->writeHMtxTable($characters);
        $tables["loca"] = $this->writeLocaTable($characters);

        $this->recalculateHeadTable($fontFile->getHeadTable());
        $this->recalculateHHeaTable($fontFile->getHHeaTable(), $fontFile->getHMtxTable());
        $this->generateMaxPTable($fontFile->getMaxPTable(), $characters);
        $fontFile->setLocaTable($this->generateLocaTable($fontFile->getGlyfTables()));
        $this->generatePostTable($fontFile->getPostTable(), $characters);

        return $this->writeSubsetFile($characters, $font->getMissingGlyphCharacter());
    }

    /**
     * @param FontFile $fontFile
     * @param Character[] $characters
     * @param Character $missingGlyphCharacter
     *
     * @return string
     * @throws \Exception
     *
     */
    private function writeSubsetFile(Font $fontFile, array $characters, Character $missingGlyphCharacter)
    {
        $streamWriter = $this->writeFontFile($fontFile);

        return $streamWriter->getStream();
    }

    /**
     * @param FontFile $fontFile
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
     * @param HeadTable $headTable
     *
     * @return HeadTable
     */
    private function recalculateHeadTable(HeadTable $headTable)
    {
        $headTable->setIndexToLocFormat(0);

        // skip the checksum calculation
        $headTable->setCheckSumAdjustment(0);

        return $headTable;
    }

    /**
     * @param HHeaTable $source
     * @param Character[] $characters
     * @param int $longHorMetricCount
     * @return HHeaTable
     */
    private function recalculateHHeaTable(HHeaTable $source, array $characters, int $longHorMetricCount)
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
            $hmtx->addLongHorMetric($character->getLongHorMetric());
        }

        return $hmtx;
    }

    /**
     * @param MaxPTable $source
     * @param Character[] $characters
     * @return MaxPTable
     */
    private function generateMaxPTable(MaxPTable $source, array $characters)
    {
        $maxPTable = new MaxPTable();

        $maxPTable->setVersion(1.0);
        $maxPTable->setNumGlyphs(count($characters));
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
     * @param Character[] $characters
     * @return string
     */
    private function writeCMapTable(array $characters)
    {
        $result = "";

        $cMapTable = $this->generateCMapTable();
        $result .= $this->tableWriter->writeCMapTable($cMapTable);

        $subtable = $this->generateSubtable(4);
        // offset needs to point to format4 from start of CMap table
        // hence 4 for cMapTable fields, 8 for subtable fields
        $subtable->setOffset(4 + 8);
        $result .= $this->tableWriter->writeCMapSubtable($subtable);

        $format4 = $this->generateCMapFormat4($characters);
        $result .= $this->tableWriter->writeCMapFormat4($format4);

        return $result;
    }

    /**
     * @param array $characters
     * @param int $cmapOffset
     *
     * @return Subtable
     */
    private function generateSubtable(int $cmapOffset): Subtable
    {
        $subtable = new Subtable();

        $subtable->setPlatformID(3);
        $subtable->setPlatformSpecificID(4);
        $subtable->setOffset($cmapOffset + 8);

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
     * @return array|Segment[]
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
    private function writeGlyfTables(array $characters)
    {
        $glyfTables = [];

        foreach ($characters as $character) {
            $glyfTables[] = $character->getGlyfTable();
        }

        return $glyfTables;
    }

    /**
     * @param Character[] $characters
     *
     * @return HMtxTable
     */
    private function writeHMtxTable(array $characters)
    {
        $hMtxTable = new HMtxTable();
        foreach ($characters as $character) {
            $hMtxTable->addLongHorMetric($character->getLongHorMetric());
        }

        return $hMtxTable;
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
     * @param StreamWriter $streamWriter
     * @param FontFile $fontFile
     * @param string $tag
     *
     * @throws \Exception
     */
    private function writeTableByTag(StreamWriter $streamWriter, FontFile $fontFile, string $tag)
    {
        switch ($tag) {
            case 'cmap':
                $this->tableWriter->writeCMapTable($fontFile->getCMapTable(), $streamWriter);
                break;
            case 'cvt ':
                $this->tableWriter->writeRawContentTable($fontFile->getCvtTable(), $streamWriter);
                break;
            case 'fpgm':
                $this->tableWriter->writeRawContentTable($fontFile->getFpgmTable(), $streamWriter);
                break;
            case 'gasp':
                $this->tableWriter->writeRawContentTable($fontFile->getGaspTable(), $streamWriter);
                break;
            case 'glyf':
                foreach ($fontFile->getGlyfTables() as $glyfTable) {
                    $this->tableWriter->writeGlyfTable($glyfTable, $streamWriter);
                }
                break;
            case 'head':
                $this->tableWriter->writeHeadTable($fontFile->getHeadTable(), $streamWriter);
                break;
            case 'hhea':
                $this->tableWriter->writeHHeaTable($fontFile->getHHeaTable(), $streamWriter);
                break;
            case 'hmtx':
                $this->tableWriter->writeHMtxTable($fontFile->getHMtxTable(), $streamWriter);
                break;
            case 'loca':
                $this->tableWriter->writeLocaTable($fontFile->getLocaTable(), $streamWriter, $fontFile->getHeadTable()->getIndexToLocFormat());
                break;
            case 'maxp':
                $this->tableWriter->writeMaxPTable($fontFile->getMaxPTable(), $streamWriter);
                break;
            case 'name':
                $this->tableWriter->writeRawContentTable($fontFile->getNameTable(), $streamWriter);
                break;
            case 'OS/2':
                $this->tableWriter->writeRawContentTable($fontFile->getOS2Table(), $streamWriter);
                break;
            case 'post':
                $this->tableWriter->writePostTable($fontFile->getPostTable(), $streamWriter);
                break;
            case 'prep':
                $this->tableWriter->writeRawContentTable($fontFile->getPrepTable(), $streamWriter);
                break;
            default:
                throw new \Exception('can not write the table with tag ' . $tag);
        }
    }

    /**
     * @param FontFile $fontFile
     *
     * @return StreamWriter
     * @throws \Exception
     *
     */
    public function writeFontFile(FontFile $fontFile)
    {
        $tables = [
            'cmap' => $fontFile->getCMapTable(),
            'cvt ' => $fontFile->getCvtTable(),
            'fpgm' => $fontFile->getFpgmTable(),
            'gasp' => $fontFile->getGaspTable(),
            'glyf' => $fontFile->getGlyfTables(),
            'head' => $fontFile->getHeadTable(),
            'hhea' => $fontFile->getHHeaTable(),
            'hmtx' => $fontFile->getHMtxTable(),
            'loca' => $fontFile->getLocaTable(),
            'maxp' => $fontFile->getMaxPTable(),
            'name' => $fontFile->getNameTable(),
            'OS/2' => $fontFile->getOS2Table(),
            'post' => $fontFile->getPostTable(),
            'prep' => $fontFile->getPrepTable(),
        ];

        foreach ($fontFile->getRawTables() as $table) {
            $tables[$table->getTag() . '_raw'] = $table;
        }

        ksort($tables);

        $tableStreamWriter = new StreamWriter();

        $offsetByTag = [];
        foreach ($tables as $tag => $table) {
            if ($table === null) {
                continue;
            }

            $rawIndex = strpos($tag, '_raw');
            if ($rawIndex !== false) {
                $this->tableWriter->writeRawTable($table, $tableStreamWriter);

                $correctedTag = substr($tag, 0, $rawIndex);
                $offsetByTag[$correctedTag] = $tableStreamWriter->getLength();
            } else {
                $offsetByTag[$tag] = $tableStreamWriter->getLength();
                $this->writeTableByTag($tableStreamWriter, $fontFile, $tag);
            }
        }

        $tableDirectoryEntries = $this->generateTableDirectoryEntries($offsetByTag, $tableStreamWriter->getLength());
        $offsetTable = $this->generateOffsetTable(\count($tableDirectoryEntries));

        $streamWriter = new StreamWriter();
        $this->tableWriter->writeOffsetTable($offsetTable, $streamWriter);

        foreach ($tableDirectoryEntries as $tableDirectoryEntry) {
            $this->tableWriter->writeTableDirectoryEntry($tableDirectoryEntry, $streamWriter);
        }

        $streamWriter->writeStream($tableStreamWriter->getStream());

        return $streamWriter;
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
     * @param PostTable $source
     * @return PostTable
     */
    private function generatePostTable(PostTable $source)
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

        return $postTable;
    }

    /**
     * @return CMapTable
     */
    private function generateCMapTable(): CMapTable
    {
        $cMapTable = new CMapTable();

        $cMapTable->setVersion(0);
        $cMapTable->setNumberSubtables(1);

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
}
