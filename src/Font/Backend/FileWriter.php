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
use PdfGenerator\Font\Frontend\File\Traits\BinaryTreeSearchableTrait;
use PdfGenerator\Font\Frontend\Utils\Format4\Segment;
use PdfGenerator\Font\IR\Structure\Character;
use PdfGenerator\Font\IR\Structure\Font;

class FileWriter
{
    /**
     * @param Font $fontFile
     * @param Character[] $characters
     * @param StreamWriter $streamWriter
     *
     * @return string
     */
    public function writeSubset(FontFile $fontFile, array $characters, StreamWriter $streamWriter)
    {
        $this->removeInvalidTables($fontFile);
        $characters = $this->sortCharactersByCodePoint($characters);

        $this->recalculateTables($fontFile, $characters);

        // add offset / tableDirectoryEntry tables
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
     * @param array $tables
     *
     * @return string
     */
    private function writeTables(array $tables)
    {
        return '';
    }

    /**
     * @param FontFile $fontFile
     * @param Character[] $characters
     */
    private function recalculateTables(FontFile $fontFile, array $characters)
    {
        $fontFile->setHMtxTable($this->generateHmtx($characters));

        $this->recalculateCMapTable($fontFile->getCMapTable(), $characters);
        $fontFile->setGlyfTables($this->getGlyfTables($characters));
        $fontFile->setHMtxTable($this->getHMtxTable($characters));
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
     * @return Subtable
     */
    private function recalculateCMapTable(CMapTable $cMapTable, array $characters)
    {
        $cMapTable->setNumberSubtables(1);

        $subtable = new Subtable();
        $subtable->setOffset(4);
        $subtable->setPlatformID(3);
        $subtable->setPlatformSpecificID(4);

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
        $format->setLength(15 + 8 * $segmentsCount); // +15 for fields; +8 because 4 arrays of 2 bytes
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
            $currentSegment->setIdDelta($character->getUnicodePoint() - $i);
        }

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
    private function getHMtxTable(array $characters)
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
}
