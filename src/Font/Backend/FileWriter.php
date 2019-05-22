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

use PdfGenerator\Font\Frontend\File\FontFile;
use PdfGenerator\Font\Frontend\File\Table\CMap\Format\Format4;
use PdfGenerator\Font\Frontend\File\Table\CMap\Subtable;
use PdfGenerator\Font\Frontend\File\Table\CMapTable;
use PdfGenerator\Font\Frontend\File\Table\GlyfTable;
use PdfGenerator\Font\Frontend\File\Table\HeadTable;
use PdfGenerator\Font\Frontend\File\Table\HHeaTable;
use PdfGenerator\Font\Frontend\File\Table\HMtxTable;
use PdfGenerator\Font\Frontend\File\Table\LocaTable;
use PdfGenerator\Font\Frontend\File\Table\MaxPTable;
use PdfGenerator\Font\Frontend\File\Table\OffsetTable;
use PdfGenerator\Font\Frontend\File\Table\TableDirectoryEntry;
use PdfGenerator\Font\Frontend\File\Traits\BinaryTreeSearchableTrait;
use PdfGenerator\Font\Frontend\Utils\Format4\Segment;
use PdfGenerator\Font\IR\Structure\Character;

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
     * @param FontFile $fontFile
     * @param Character[] $characters
     *
     * @throws \Exception
     *
     * @return string
     */
    public function writeSubset(FontFile $fontFile, array $characters)
    {
        $this->removeInvalidTables($fontFile);
        $characters = $this->prepareCharacters($fontFile, $characters);

        $this->recalculateTables($fontFile, $characters);

        $streamWriter = $this->writeFontFile($fontFile);

        return $streamWriter->getStream();
    }

    /**
     * @param FontFile $fontFile
     * @param Character[] $characters
     *
     * @return Character[]
     */
    private function prepareCharacters(FontFile $fontFile, array $characters)
    {
        $orderedCharacters = $this->sortCharactersByCodePoint($characters);

        $missingGlyphCharacter = new Character();
        $missingGlyphCharacter->setLongHorMetric($fontFile->getHMtxTable()->getLongHorMetrics()[0]);
        $missingGlyphCharacter->setGlyfTable($fontFile->getGlyfTables()[0]);
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
     * @param FontFile $fontFile
     */
    private function removeInvalidTables(FontFile $fontFile)
    {
        /*
         * can keep cvt, fpgm, gasp
         */
        $fontFile->setGDEFTable(null);
        $fontFile->setGPOSTable(null);
        $fontFile->setGSUBTable(null);
        $fontFile->setPostTable(null);
    }

    /**
     * @param FontFile $fontFile
     * @param Character[] $characters
     */
    private function recalculateTables(FontFile $fontFile, array $characters)
    {
        $fontFile->setHMtxTable($this->generateHmtx($characters));

        $fontFile->setCMapTable($this->generateCMapTable($characters));
        $fontFile->setGlyfTables($this->getGlyfTables($characters));
        $fontFile->setHMtxTable($this->generateHMtxTable($characters));
        $this->recalculateHeadTable($fontFile->getHeadTable());
        $this->recalculateHHeaTable($fontFile->getHHeaTable(), $fontFile->getHMtxTable());
        $this->recalculateMaxPTable($fontFile->getMaxPTable(), $characters);
        $fontFile->setLocaTable($this->generateLocaTable($fontFile->getGlyfTables()));
    }

    /**
     * @param HHeaTable $hHeaTable
     * @param HMtxTable $hMtxTable
     */
    private function recalculateHHeaTable(HHeaTable $hHeaTable, HMtxTable $hMtxTable)
    {
        $hHeaTable->setNumOfLongHorMetrics(\count($hMtxTable->getLongHorMetrics()));
    }

    /**
     * @param Character[] $characters
     *
     * @return HMtxTable
     */
    private function generateHmtx(array $characters)
    {
        $hmtx = new HMtxTable();

        foreach ($characters as $character) {
            $hmtx->addLongHorMetric($character->getLongHorMetric());
        }

        return $hmtx;
    }

    /**
     * @param MaxPTable $maxPTable
     * @param array $characters
     */
    private function recalculateMaxPTable(MaxPTable $maxPTable, array $characters)
    {
        $maxPTable->setNumGlyphs(\count($characters));
    }

    /**
     * @param CMapTable $cMapTable
     * @param Character[] $characters
     *
     * @return CMapTable
     */
    private function generateCMapTable(array $characters)
    {
        $cMapTable = new CMapTable();
        $cMapTable->setVersion(0);
        $cMapTable->setNumberSubtables(1);

        $subtable = $this->generateSubtable($characters, 4);
        $cMapTable->addSubtable($subtable);

        return $cMapTable;
    }

    /**
     * @param array $characters
     * @param int $cmapOffset
     *
     * @return Subtable
     */
    private function generateSubtable(array $characters, int $cmapOffset): Subtable
    {
        $subtable = new Subtable();
        $subtable->setPlatformID(3);
        $subtable->setPlatformSpecificID(4);
        $subtable->setOffset($cmapOffset + 8);

        $format = $this->generateFormat4($characters);
        $subtable->setFormat($format);

        return $subtable;
    }

    /**
     * @param Character[] $characters
     *
     * @return Format4
     */
    private function generateFormat4(array $characters)
    {
        $segments = $this->generateSegments($characters);
        $segmentsCount = \count($segments);

        $format = new Format4();
        $format->setLength(8 * 2 + 4 * 2 * $segmentsCount); // 8 fields; 4 arrays of size 2 per entry
        $format->setLanguage(0);
        $format->setSegCountX2($segmentsCount * 2);
        $this->setBinaryTreeSearchableProperties($format, $format->getSegCountX2());
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
     * @param BinaryTreeSearchableTrait $binaryTreeSearchable
     * @param int $numberOfEntries
     */
    private function setBinaryTreeSearchableProperties($binaryTreeSearchable, int $numberOfEntries)
    {
        $powerOfTwo = (int)log($numberOfEntries, 2);

        $binaryTreeSearchable->setSearchRange(pow(2, $powerOfTwo));
        $binaryTreeSearchable->setEntrySelector($powerOfTwo);
        $binaryTreeSearchable->setRangeShift($numberOfEntries - $binaryTreeSearchable->getSearchRange());
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
    private function getGlyfTables(array $characters)
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
    private function generateHMtxTable(array $characters)
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
     * @param $table
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
                $this->tableWriter->writeRawContentTable($fontFile->getPostTable(), $streamWriter);
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
     * @throws \Exception
     *
     * @return StreamWriter
     */
    private function writeFontFile(FontFile $fontFile)
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
        $this->setBinaryTreeSearchableProperties($offsetTable, $numTables);

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
}
