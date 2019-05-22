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
use PdfGenerator\Font\Frontend\File\Table\Interfaces\WritableTableVisitorInterface;
use PdfGenerator\Font\Frontend\File\Table\LocaTable;
use PdfGenerator\Font\Frontend\File\Table\MaxPTable;
use PdfGenerator\Font\Frontend\File\Table\OffsetTable;
use PdfGenerator\Font\Frontend\File\Table\RawTable;
use PdfGenerator\Font\Frontend\File\Table\TableDirectoryEntry;
use PdfGenerator\Font\Frontend\File\Traits\BinaryTreeSearchableTrait;
use PdfGenerator\Font\Frontend\File\Traits\BoundingBoxTrait;
use PdfGenerator\Font\Frontend\File\Traits\RawContent;

class WriteTablesVisitor implements WritableTableVisitorInterface
{
    /**
     * @var StreamWriter
     */
    private $writer;

    /**
     * @var int
     */
    private $indexToLocFormat;

    /**
     * WriteTablesVisitor constructor.
     *
     * @param StreamWriter $writer
     * @param int $indexToLocFormat
     */
    public function __construct(StreamWriter $writer, int $indexToLocFormat)
    {
        $this->writer = $writer;
        $this->indexToLocFormat = $indexToLocFormat;
    }

    /**
     * @param CMapTable $cMapTable
     */
    public function visitCMap(CMapTable $cMapTable)
    {
        $this->writer->writeInt16($cMapTable->getVersion());
    }

    /**
     * @return StreamWriter
     */
    public function getWriter(): StreamWriter
    {
        return $this->writer;
    }

    /**
     * @param OffsetTable $offsetTable
     *
     * @throws \Exception
     */
    private function writeOffsetTable(OffsetTable $offsetTable)
    {
        $this->writer->writeUInt32($offsetTable->getScalerType());
        $this->writer->writeUInt16($offsetTable->getNumTables());
        $this->writeBinaryTreeSearchableUInt16($offsetTable);
    }

    /**
     * @param BinaryTreeSearchableTrait $binaryTreeSearchable
     */
    public function writeBinaryTreeSearchableUInt16($binaryTreeSearchable)
    {
        $this->writer->writeUInt16($binaryTreeSearchable->getSearchRange());
        $this->writer->writeUInt16($binaryTreeSearchable->getEntrySelector());
        $this->writer->writeUInt16($binaryTreeSearchable->getRangeShift());
    }

    /**
     * @param BoundingBoxTrait $boundingBoxTrait
     */
    public function writeBoundingBoxInt16($boundingBoxTrait)
    {
        $this->writer->writeInt16($boundingBoxTrait->getXMin());
        $this->writer->writeInt16($boundingBoxTrait->getXMax());
        $this->writer->writeInt16($boundingBoxTrait->getYMin());
        $this->writer->writeInt16($boundingBoxTrait->getYMax());
    }

    /**
     * @param BoundingBoxTrait $boundingBoxTrait
     */
    public function writeBoundingBoxFWORD($boundingBoxTrait)
    {
        $this->writer->writeFWORD($boundingBoxTrait->getXMin());
        $this->writer->writeFWORD($boundingBoxTrait->getXMax());
        $this->writer->writeFWORD($boundingBoxTrait->getYMin());
        $this->writer->writeFWORD($boundingBoxTrait->getYMax());
    }

    /**
     * @param TableDirectoryEntry $tableDirectoryEntry
     */
    private function writeTableDirectoryEntry(TableDirectoryEntry $tableDirectoryEntry)
    {
        $this->writer->writeTagFromString($tableDirectoryEntry->getTag());
        $this->writer->writeUInt32($tableDirectoryEntry->getCheckSum());
        $this->writer->writeOffset32($tableDirectoryEntry->getOffset());
        $this->writer->writeUInt32($tableDirectoryEntry->getLength());
    }

    /**
     * @param CMapTable $cMapTable
     *
     * @throws \Exception
     */
    private function writeCMapTable(CMapTable $cMapTable)
    {
        $this->writer->writeUInt16($cMapTable->getVersion());
        $this->writer->writeUInt16($cMapTable->getNumberSubtables());

        foreach ($cMapTable->getSubtables() as $subtable) {
            $this->writeCMapSubtable($subtable);
        }
    }

    /**
     * @param Subtable $subtable
     *
     * @throws \Exception
     */
    private function writeCMapSubtable(Subtable $subtable)
    {
        $this->writer->writeUInt16($subtable->getPlatformID());
        $this->writer->writeUInt16($subtable->getPlatformSpecificID());
        $this->writer->writeOffset32($subtable->getOffset());

        $format = $subtable->getFormat();
        if (!$format instanceof Format4) {
            throw new \Exception('format not supported for writing');
        }

        $this->writeFormat4($format);
    }

    /**
     * @param Format4 $format
     */
    private function writeFormat4(Format4 $format)
    {
        $this->writer->writeUInt16($format->getLength());
        $this->writer->writeUInt16($format->getLanguage());

        $this->writer->writeUInt16($format->getSegCountX2());
        $this->writeBinaryTreeSearchableUInt16($format);

        $this->writer->writeUInt16Array($format->getEndCodes());
        $this->writer->writeUInt16($format->getReservedPad());
        $this->writer->writeUInt16Array($format->getStartCodes());
        $this->writer->writeInt16Array($format->getIdDeltas());
        $this->writer->writeUInt16Array($format->getIdRangeOffsets());
        $this->writer->writeUInt16Array($format->getGlyphIndexArray());
    }

    /**
     * @param GlyfTable $glyfTable
     *
     * @throws \Exception
     */
    private function writeGlyfTable(GlyfTable $glyfTable)
    {
        $this->writer->writeInt16($glyfTable->getNumberOfContours());
        $this->writeBoundingBoxFWORD($glyfTable);

        $this->writer->writeRaw($glyfTable->getContent());
    }

    /**
     * @param LocaTable $locaTable
     */
    private function writeLocaTable(LocaTable $locaTable)
    {
        if ($this->indexToLocFormat === 0) {
            $this->writer->writeOffset16Array($locaTable->getOffsets());
        } else {
            $this->writer->writeOffset32Array($locaTable->getOffsets());
        }
    }

    /**
     * @param MaxPTable $maxPTable
     */
    private function writeMaxPTable(MaxPTable $maxPTable)
    {
        $this->writer->writeFixed($maxPTable->getVersion());
        $this->writer->writeUInt16($maxPTable->getNumGlyphs());
        $this->writer->writeUInt16($maxPTable->getMaxPoints());
        $this->writer->writeUInt16($maxPTable->getMaxContours());
        $this->writer->writeUInt16($maxPTable->getMaxCompositePoints());
        $this->writer->writeUInt16($maxPTable->getMaxCompositeContours());
        $this->writer->writeUInt16($maxPTable->getMaxZones());
        $this->writer->writeUInt16($maxPTable->getMaxTwilightPoints());
        $this->writer->writeUInt16($maxPTable->getMaxStorage());
        $this->writer->writeUInt16($maxPTable->getMaxFunctionDefs());
        $this->writer->writeUInt16($maxPTable->getMaxInstructionDefs());
        $this->writer->writeUInt16($maxPTable->getMaxStackElements());
        $this->writer->writeUInt16($maxPTable->getMaxSizeOfInstructions());
        $this->writer->writeUInt16($maxPTable->getMaxComponentElements());
        $this->writer->writeUInt16($maxPTable->getMaxComponentDepth());
    }

    /**
     * @param HeadTable $headTable
     */
    private function writeHeadTable(HeadTable $headTable)
    {
        $this->writer->writeUInt16($headTable->getMajorVersion());
        $this->writer->writeUInt16($headTable->getMinorVersion());
        $this->writer->writeFixed($headTable->getFontRevision());
        $this->writer->writeUInt32($headTable->getCheckSumAdjustment());
        $this->writer->writeUInt32($headTable->getMagicNumber());
        $this->writer->writeUInt16($headTable->getFlags());
        $this->writer->writeUInt16($headTable->getUnitsPerEm());
        $this->writer->writeLONGDATETIME($headTable->getCreated());
        $this->writer->writeLONGDATETIME($headTable->getModified());

        $this->writeBoundingBoxInt16($headTable);

        $this->writer->writeUInt16($headTable->getMacStyle());
        $this->writer->writeUInt16($headTable->getLowestRecPPEM());
        $this->writer->writeInt16($headTable->getFontDirectionHints());
        $this->writer->writeInt16($headTable->getIndexToLocFormat());
        $this->writer->writeInt16($headTable->getGlyphDataFormat());
    }

    /**
     * @param RawContent $rawContentTable
     */
    private function writeRawContentTable($rawContentTable)
    {
        $this->writer->writeRaw($rawContentTable->getContent());
    }

    /**
     * @param RawTable $rawTable
     */
    private function writeRawTable(RawTable $rawTable)
    {
        $this->writer->writeRaw($rawTable->getContent());
    }

    /**
     * @param HHeaTable $hHeaTable
     */
    private function writeHHeaTable(HHeaTable $hHeaTable)
    {
        $this->writer->writeFixed($hHeaTable->getVersion());
        $this->writer->writeFWORD($hHeaTable->getAscent());
        $this->writer->writeFWORD($hHeaTable->getDecent());
        $this->writer->writeFWORD($hHeaTable->getLineGap());
        $this->writer->writeUFWORD($hHeaTable->getAdvanceWidthMax());
        $this->writer->writeFWORD($hHeaTable->getMinLeftSideBearing());
        $this->writer->writeFWORD($hHeaTable->getMinRightSideBearing());
        $this->writer->writeFWORD($hHeaTable->getXMaxExtent());
        $this->writer->writeInt16($hHeaTable->getCaretSlopeRise());
        $this->writer->writeInt16($hHeaTable->getCaretSlopeRun());
        $this->writer->writeFWORD($hHeaTable->getCaretOffset());

        // skip reserved characters
        $this->writer->writeUInt32(0);
        $this->writer->writeUInt32(0);

        $this->writer->writeInt16($hHeaTable->getMetricDataFormat());
        $this->writer->writeUInt16($hHeaTable->getNumOfLongHorMetrics());
    }

    /**
     * @param HMtxTable $hMtxTable
     */
    private function writeHMtxTable(HMtxTable $hMtxTable)
    {
        foreach ($hMtxTable->getLongHorMetrics() as $longHorMetric) {
            $this->writer->writeUInt16($longHorMetric->getAdvanceWidth());
            $this->writer->writeInt16($longHorMetric->getLeftSideBearing());
        }

        foreach ($hMtxTable->getLeftSideBearings() as $leftSideBearing) {
            $this->writer->writeFWORD($leftSideBearing);
        }
    }
}
