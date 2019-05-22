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
use PdfGenerator\Font\Frontend\File\Table\RawTable;
use PdfGenerator\Font\Frontend\File\Table\TableDirectoryEntry;
use PdfGenerator\Font\Frontend\File\Traits\BinaryTreeSearchableTrait;
use PdfGenerator\Font\Frontend\File\Traits\BoundingBoxTrait;
use PdfGenerator\Font\Frontend\File\Traits\RawContent;

class TableWriter
{
    /**
     * @param OffsetTable $offsetTable
     * @param StreamWriter $writer
     */
    public function writeOffsetTable(OffsetTable $offsetTable, StreamWriter $writer)
    {
        $writer->writeUInt32($offsetTable->getScalerType());
        $writer->writeUInt16($offsetTable->getNumTables());
        $this->writeBinaryTreeSearchableUInt16($offsetTable, $writer);
    }

    /**
     * @param TableDirectoryEntry $tableDirectoryEntry
     * @param StreamWriter $writer
     */
    public function writeTableDirectoryEntry(TableDirectoryEntry $tableDirectoryEntry, StreamWriter $writer)
    {
        $writer->writeTagFromString($tableDirectoryEntry->getTag());
        $writer->writeUInt32($tableDirectoryEntry->getCheckSum());
        $writer->writeOffset32($tableDirectoryEntry->getOffset());
        $writer->writeUInt32($tableDirectoryEntry->getLength());
    }

    /**
     * @param CMapTable $cMapTable
     * @param StreamWriter $writer
     *
     * @throws \Exception
     */
    public function writeCMapTable(CMapTable $cMapTable, StreamWriter $writer)
    {
        $writer->writeUInt16($cMapTable->getVersion());
        $writer->writeUInt16($cMapTable->getNumberSubtables());

        foreach ($cMapTable->getSubtables() as $subtable) {
            $this->writeCMapSubtable($subtable, $writer);
        }
    }

    /**
     * @param Subtable $subtable
     * @param StreamWriter $writer
     *
     * @throws \Exception
     */
    private function writeCMapSubtable(Subtable $subtable, StreamWriter $writer)
    {
        $writer->writeUInt16($subtable->getPlatformID());
        $writer->writeUInt16($subtable->getPlatformSpecificID());
        $writer->writeOffset32($subtable->getOffset());

        $format = $subtable->getFormat();
        if (!$format instanceof Format4) {
            throw new \Exception('format not supported for writing');
        }

        $this->writeFormat4($format, $writer);
    }

    /**
     * @param Format4 $format
     * @param StreamWriter $writer
     */
    private function writeFormat4(Format4 $format, StreamWriter $writer)
    {
        $writer->writeUInt16(4);
        $writer->writeUInt16($format->getLength());
        $writer->writeUInt16($format->getLanguage());

        $writer->writeUInt16($format->getSegCountX2());
        $this->writeBinaryTreeSearchableUInt16($format, $writer);

        $writer->writeUInt16Array($format->getEndCodes());
        $writer->writeUInt16($format->getReservedPad());
        $writer->writeUInt16Array($format->getStartCodes());
        $writer->writeInt16Array($format->getIdDeltas());
        $writer->writeUInt16Array($format->getIdRangeOffsets());
        $writer->writeUInt16Array($format->getGlyphIndexArray());
    }

    /**
     * @param GlyfTable $glyfTable
     * @param StreamWriter $writer
     */
    public function writeGlyfTable(GlyfTable $glyfTable, StreamWriter $writer)
    {
        $writer->writeInt16($glyfTable->getNumberOfContours());
        $this->writeBoundingBoxFWORD($glyfTable, $writer);

        $writer->writeStream($glyfTable->getContent());
    }

    /**
     * @param LocaTable $locaTable
     * @param StreamWriter $writer
     * @param int $indexToLocFormat
     */
    public function writeLocaTable(LocaTable $locaTable, StreamWriter $writer, int $indexToLocFormat)
    {
        if ($indexToLocFormat === 0) {
            $writer->writeOffset16Array($locaTable->getOffsets());
        } else {
            $writer->writeOffset32Array($locaTable->getOffsets());
        }
    }

    /**
     * @param MaxPTable $maxPTable
     * @param StreamWriter $writer
     */
    public function writeMaxPTable(MaxPTable $maxPTable, StreamWriter $writer)
    {
        $writer->writeFixed($maxPTable->getVersion());
        $writer->writeUInt16($maxPTable->getNumGlyphs());
        $writer->writeUInt16($maxPTable->getMaxPoints());
        $writer->writeUInt16($maxPTable->getMaxContours());
        $writer->writeUInt16($maxPTable->getMaxCompositePoints());
        $writer->writeUInt16($maxPTable->getMaxCompositeContours());
        $writer->writeUInt16($maxPTable->getMaxZones());
        $writer->writeUInt16($maxPTable->getMaxTwilightPoints());
        $writer->writeUInt16($maxPTable->getMaxStorage());
        $writer->writeUInt16($maxPTable->getMaxFunctionDefs());
        $writer->writeUInt16($maxPTable->getMaxInstructionDefs());
        $writer->writeUInt16($maxPTable->getMaxStackElements());
        $writer->writeUInt16($maxPTable->getMaxSizeOfInstructions());
        $writer->writeUInt16($maxPTable->getMaxComponentElements());
        $writer->writeUInt16($maxPTable->getMaxComponentDepth());
    }

    /**
     * @param HeadTable $headTable
     * @param StreamWriter $writer
     */
    public function writeHeadTable(HeadTable $headTable, StreamWriter $writer)
    {
        $writer->writeUInt16($headTable->getMajorVersion());
        $writer->writeUInt16($headTable->getMinorVersion());
        $writer->writeFixed($headTable->getFontRevision());
        $writer->writeUInt32($headTable->getCheckSumAdjustment());
        $writer->writeUInt32($headTable->getMagicNumber());
        $writer->writeUInt16($headTable->getFlags());
        $writer->writeUInt16($headTable->getUnitsPerEm());
        $writer->writeLONGDATETIME($headTable->getCreated());
        $writer->writeLONGDATETIME($headTable->getModified());

        $this->writeBoundingBoxInt16($headTable, $writer);

        $writer->writeUInt16($headTable->getMacStyle());
        $writer->writeUInt16($headTable->getLowestRecPPEM());
        $writer->writeInt16($headTable->getFontDirectionHints());
        $writer->writeInt16($headTable->getIndexToLocFormat());
        $writer->writeInt16($headTable->getGlyphDataFormat());
    }

    /**
     * @param RawContent $rawContentTable
     * @param StreamWriter $writer
     */
    public function writeRawContentTable($rawContentTable, StreamWriter $writer)
    {
        $writer->writeStream($rawContentTable->getContent());
    }

    /**
     * @param RawTable $rawTable
     * @param StreamWriter $writer
     */
    public function writeRawTable(RawTable $rawTable, StreamWriter $writer)
    {
        $writer->writeStream($rawTable->getContent());
    }

    /**
     * @param HHeaTable $hHeaTable
     * @param StreamWriter $writer
     */
    public function writeHHeaTable(HHeaTable $hHeaTable, StreamWriter $writer)
    {
        $writer->writeFixed($hHeaTable->getVersion());
        $writer->writeFWORD($hHeaTable->getAscent());
        $writer->writeFWORD($hHeaTable->getDecent());
        $writer->writeFWORD($hHeaTable->getLineGap());
        $writer->writeUFWORD($hHeaTable->getAdvanceWidthMax());
        $writer->writeFWORD($hHeaTable->getMinLeftSideBearing());
        $writer->writeFWORD($hHeaTable->getMinRightSideBearing());
        $writer->writeFWORD($hHeaTable->getXMaxExtent());
        $writer->writeInt16($hHeaTable->getCaretSlopeRise());
        $writer->writeInt16($hHeaTable->getCaretSlopeRun());
        $writer->writeFWORD($hHeaTable->getCaretOffset());

        // skip reserved characters
        $writer->writeUInt32(0);
        $writer->writeUInt32(0);

        $writer->writeInt16($hHeaTable->getMetricDataFormat());
        $writer->writeUInt16($hHeaTable->getNumOfLongHorMetrics());
    }

    /**
     * @param HMtxTable $hMtxTable
     * @param StreamWriter $writer
     */
    public function writeHMtxTable(HMtxTable $hMtxTable, StreamWriter $writer)
    {
        foreach ($hMtxTable->getLongHorMetrics() as $longHorMetric) {
            $writer->writeUInt16($longHorMetric->getAdvanceWidth());
            $writer->writeInt16($longHorMetric->getLeftSideBearing());
        }

        foreach ($hMtxTable->getLeftSideBearings() as $leftSideBearing) {
            $writer->writeFWORD($leftSideBearing);
        }
    }

    /**
     * @param BinaryTreeSearchableTrait $binaryTreeSearchable
     * @param StreamWriter $writer
     */
    private function writeBinaryTreeSearchableUInt16($binaryTreeSearchable, StreamWriter $writer)
    {
        $writer->writeUInt16($binaryTreeSearchable->getSearchRange());
        $writer->writeUInt16($binaryTreeSearchable->getEntrySelector());
        $writer->writeUInt16($binaryTreeSearchable->getRangeShift());
    }

    /**
     * @param BoundingBoxTrait $boundingBoxTrait
     * @param StreamWriter $writer
     */
    private function writeBoundingBoxInt16($boundingBoxTrait, StreamWriter $writer)
    {
        $writer->writeInt16($boundingBoxTrait->getXMin());
        $writer->writeInt16($boundingBoxTrait->getXMax());
        $writer->writeInt16($boundingBoxTrait->getYMin());
        $writer->writeInt16($boundingBoxTrait->getYMax());
    }

    /**
     * @param BoundingBoxTrait $boundingBoxTrait
     * @param StreamWriter $writer
     */
    private function writeBoundingBoxFWORD($boundingBoxTrait, StreamWriter $writer)
    {
        $writer->writeFWORD($boundingBoxTrait->getXMin());
        $writer->writeFWORD($boundingBoxTrait->getXMax());
        $writer->writeFWORD($boundingBoxTrait->getYMin());
        $writer->writeFWORD($boundingBoxTrait->getYMax());
    }
}
