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

use PdfGenerator\Font\Backend\File\Table\CMap\Format\Format4;
use PdfGenerator\Font\Backend\File\Table\CMap\FormatVisitor;
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
use PdfGenerator\Font\Backend\File\Table\RawTable;
use PdfGenerator\Font\Backend\File\Table\TableDirectoryEntry;
use PdfGenerator\Font\Backend\File\Traits\RawContent;
use PdfGenerator\Font\Backend\File\Traits\Writer;

class TableWriter
{
    /**
     * @var FormatVisitor
     */
    private $cMapFormatVisitor;

    /**
     * @var File\Table\Post\FormatVisitor
     */
    private $postFormatVisitor;

    /**
     * TableWriter constructor.
     * @param FormatVisitor $cMapFormatVisitor
     * @param File\Table\Post\FormatVisitor $postFormatVisitor
     */
    public function __construct(FormatVisitor $cMapFormatVisitor, File\Table\Post\FormatVisitor $postFormatVisitor)
    {
        $this->cMapFormatVisitor = $cMapFormatVisitor;
        $this->postFormatVisitor = $postFormatVisitor;
    }

    /**
     * @param OffsetTable $offsetTable
     * @return string
     */
    public function writeOffsetTable(OffsetTable $offsetTable)
    {
        $writer = new StreamWriter();

        $writer->writeUInt32($offsetTable->getScalerType());
        $writer->writeUInt16($offsetTable->getNumTables());
        Writer::writeBinaryTreeSearchableUInt16($offsetTable, $writer);

        return $writer->getStream();
    }

    /**
     * @param TableDirectoryEntry $tableDirectoryEntry
     * @return StreamWriter
     */
    public function writeTableDirectoryEntry(TableDirectoryEntry $tableDirectoryEntry)
    {
        $writer = new StreamWriter();

        $writer->writeTagFromString($tableDirectoryEntry->getTag());
        $writer->writeUInt32($tableDirectoryEntry->getCheckSum());
        $writer->writeOffset32($tableDirectoryEntry->getOffset());
        $writer->writeUInt32($tableDirectoryEntry->getLength());

        return $writer;
    }

    /**
     * @param CMapTable $cMapTable
     * @return string
     */
    public function writeCMapTable(CMapTable $cMapTable)
    {
        $writer = new StreamWriter();

        $writer->writeUInt16($cMapTable->getVersion());
        $writer->writeUInt16($cMapTable->getNumberSubtables());

        $formatStreamWriter = new StreamWriter();
        $offsetBySubtable = [];
        foreach ($cMapTable->getSubtables() as $subtable) {
            $offsetBySubtable[] = $formatStreamWriter->getLength();
            $subtable->getFormat()->accept($this->cMapFormatVisitor, $formatStreamWriter);
        }

        $subtableSize = 
        $subtableOffset = count($cMapTable->getSubtables())*
        for ($i = 0; $i < count($cMapTable->getSubtables()); $i++) {
            $writer->writeUInt16($subtable->getPlatformID());
            $writer->writeUInt16($subtable->getPlatformSpecificID());
            $writer->writeOffset32(4 + );
        }

        return $writer->getStream();
    }

    /**
     * @param Subtable $subtable
     * @return string
     */
    private static function writeCMapSubtable(Subtable $subtable, StreamWriter $writer)
    {

        return $writer->getStream();
    }

    /**
     * @param GlyfTable $glyfTable
     * @return string
     */
    public function writeGlyfTable(GlyfTable $glyfTable)
    {
        $writer = new StreamWriter();

        $writer->writeInt16($glyfTable->getNumberOfContours());
        Writer::writeBoundingBoxFWORD($glyfTable, $writer);

        $writer->writeStream($glyfTable->getContent());

        return $writer->getStream();
    }

    /**
     * @param LocaTable $locaTable
     * @param int $indexToLocFormat
     * @return string
     */
    public function writeLocaTable(LocaTable $locaTable, int $indexToLocFormat)
    {
        $writer = new StreamWriter();

        if ($indexToLocFormat === 0) {
            $writer->writeOffset16Array($locaTable->getOffsets());
        } else {
            $writer->writeOffset32Array($locaTable->getOffsets());
        }

        return $writer->getStream();
    }

    /**
     * @param MaxPTable $maxPTable
     * @return StreamWriter
     */
    public function writeMaxPTable(MaxPTable $maxPTable)
    {
        $writer = new StreamWriter();

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

        return $writer;
    }

    /**
     * @param HeadTable $headTable
     * @return StreamWriter
     */
    public function writeHeadTable(HeadTable $headTable)
    {
        $writer = new StreamWriter();

        $writer->writeUInt16($headTable->getMajorVersion());
        $writer->writeUInt16($headTable->getMinorVersion());
        $writer->writeFixed($headTable->getFontRevision());
        $writer->writeUInt32($headTable->getCheckSumAdjustment());
        $writer->writeUInt32($headTable->getMagicNumber());
        $writer->writeUInt16($headTable->getFlags());
        $writer->writeUInt16($headTable->getUnitsPerEm());
        $writer->writeLONGDATETIME($headTable->getCreated());
        $writer->writeLONGDATETIME($headTable->getModified());

        Writer::writeBoundingBoxInt16($headTable, $writer);

        $writer->writeUInt16($headTable->getMacStyle());
        $writer->writeUInt16($headTable->getLowestRecPPEM());
        $writer->writeInt16($headTable->getFontDirectionHints());
        $writer->writeInt16($headTable->getIndexToLocFormat());
        $writer->writeInt16($headTable->getGlyphDataFormat());

        return $writer;
    }

    /**
     * @param RawTable $rawTable
     * @return string
     */
    public function writeRawTable(RawTable $rawTable)
    {
        $writer = new StreamWriter();

        $writer->writeStream($rawTable->getContent());

        return $writer->getStream();
    }

    /**
     * @param HHeaTable $hHeaTable
     * @return string
     */
    public function writeHHeaTable(HHeaTable $hHeaTable)
    {
        $writer = new StreamWriter();

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

        return $writer->getStream();
    }

    /**
     * @param HMtxTable $hMtxTable
     * @return string
     */
    public function writeHMtxTable(HMtxTable $hMtxTable)
    {
        $writer = new StreamWriter();

        foreach ($hMtxTable->getLongHorMetrics() as $longHorMetric) {
            $writer->writeUInt16($longHorMetric->getAdvanceWidth());
            $writer->writeInt16($longHorMetric->getLeftSideBearing());
        }

        foreach ($hMtxTable->getLeftSideBearings() as $leftSideBearing) {
            $writer->writeFWORD($leftSideBearing);
        }

        return $writer->getStream();
    }

    /**
     * @param PostTable $postTable
     * @return string
     */
    public function writePostTable(PostTable $postTable)
    {
        $streamWriter = new StreamWriter();

        $streamWriter->writeFixed($postTable->getVersion());
        $streamWriter->writeFixed($postTable->getItalicAngle());
        $streamWriter->writeFWORD($postTable->getUnderlinePosition());
        $streamWriter->writeFWORD($postTable->getUnderlineThickness());

        $streamWriter->writeUInt32($postTable->getIsFixedPitch());
        $streamWriter->writeUInt32($postTable->getMinMemType42());
        $streamWriter->writeUInt32($postTable->getMaxMemType42());
        $streamWriter->writeUInt32($postTable->getMinMemType1());
        $streamWriter->writeUInt32($postTable->getMaxMemType1());

        self::writePostFormat2($postTable->getFormat(), $streamWriter);

        return $streamWriter->getStream();
    }

    /**
     * @param Format2 $format
     * @param StreamWriter $streamWriter
     */
    private static function writePostFormat2(Format2 $format, StreamWriter $streamWriter)
    {
        $streamWriter->writeUInt16($format->getNumGlyphs());
        $streamWriter->writeUInt16Array($format->getGlyphNameIndex());
        $streamWriter->writeStream($format->getNames());
    }
}
