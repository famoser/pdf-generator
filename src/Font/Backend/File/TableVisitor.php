<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Backend\File;

use PdfGenerator\Font\Backend\File\Table\CMap\FormatVisitor;
use PdfGenerator\Font\Backend\File\Traits\Writer;
use PdfGenerator\Font\Backend\StreamWriter;

class TableVisitor
{
    /**
     * @var FormatVisitor
     */
    private $cMapFormatVisitor;

    /**
     * @var Table\Post\FormatVisitor
     */
    private $postFormatVisitor;

    /**
     * @var int
     */
    public static $indexToLocFormat = 0;

    /**
     * TableWriter constructor.
     *
     * @param FormatVisitor $cMapFormatVisitor
     * @param Table\Post\FormatVisitor $postFormatVisitor
     */
    public function __construct(FormatVisitor $cMapFormatVisitor, Table\Post\FormatVisitor $postFormatVisitor)
    {
        $this->cMapFormatVisitor = $cMapFormatVisitor;
        $this->postFormatVisitor = $postFormatVisitor;
    }

    /**
     * @return TableVisitor
     */
    public static function create()
    {
        $cmapFormatVisitor = new FormatVisitor();
        $postFormatVisitor = new Table\Post\FormatVisitor();

        return new self($cmapFormatVisitor, $postFormatVisitor);
    }

    /**
     * @param Table\CMapTable $cMapTable
     *
     * @return string
     */
    public function visitCMapTable(Table\CMapTable $cMapTable): string
    {
        $writer = new StreamWriter();

        $writer->writeUInt16($cMapTable->getVersion());
        $writer->writeUInt16($cMapTable->getNumberSubtables());
        $cMapOffset = 4;

        $formatStreamWriter = new StreamWriter();
        $offsetBySubtable = [];
        foreach ($cMapTable->getSubtables() as $subtable) {
            $offsetBySubtable[] = $formatStreamWriter->getLength();
            $subtable->getFormat()->accept($this->cMapFormatVisitor, $formatStreamWriter);
        }

        $subtableSize = 8;
        $subtableOffset = \count($cMapTable->getSubtables()) * $subtableSize;
        $formatOffset = $cMapOffset + $subtableOffset;
        for ($i = 0; $i < \count($cMapTable->getSubtables()); ++$i) {
            $formatOffset += $offsetBySubtable[$i];
            $subTable = $cMapTable->getSubtables()[$i];

            $writer->writeUInt16($subTable->getPlatformID());
            $writer->writeUInt16($subTable->getPlatformSpecificID());
            $writer->writeOffset32($formatOffset);
        }

        $writer->writeStream($formatStreamWriter->getStream());

        return $writer->getStream();
    }

    /**
     * @param Table\GlyfTable $glyfTable
     *
     * @return string
     */
    public function visitGlyfTable(Table\GlyfTable $glyfTable): string
    {
        $writer = new StreamWriter();

        $writer->writeInt16($glyfTable->getNumberOfContours());
        Writer::writeBoundingBoxFWORD($glyfTable, $writer);

        $writer->writeStream($glyfTable->getContent());

        return $writer->getStream();
    }

    /**
     * @param Table\HeadTable $headTable
     *
     * @return string
     */
    public function visitHeadTable(Table\HeadTable $headTable): string
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
        $writer->writeInt16(self::$indexToLocFormat);
        $writer->writeInt16($headTable->getGlyphDataFormat());

        return $writer->getStream();
    }

    /**
     * @param Table\HHeaTable $hHeaTable
     *
     * @return string
     */
    public function visitHHeaTable(Table\HHeaTable $hHeaTable): string
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
     * @param Table\HMtxTable $hMtxTable
     *
     * @return string
     */
    public function visitHMtxTable(Table\HMtxTable $hMtxTable): string
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
     * @param Table\LocaTable $locaTable
     *
     * @return string
     */
    public function visitLocaTable(Table\LocaTable $locaTable): string
    {
        $writer = new StreamWriter();

        if (self::$indexToLocFormat === 0) {
            $writer->writeOffset16Array($locaTable->getOffsets());
        } else {
            $writer->writeOffset32Array($locaTable->getOffsets());
        }

        return $writer->getStream();
    }

    /**
     * @param Table\MaxPTable $maxPTable
     *
     * @return string
     */
    public function visitMaxPTable(Table\MaxPTable $maxPTable): string
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

        return $writer->getStream();
    }

    /**
     * @param Table\OffsetTable $offsetTable
     *
     * @return string
     */
    public function visitOffsetTable(Table\OffsetTable $offsetTable): string
    {
        $writer = new StreamWriter();

        $writer->writeUInt32($offsetTable->getScalerType());
        $writer->writeUInt16($offsetTable->getNumTables());
        Writer::writeBinaryTreeSearchableUInt16($offsetTable, $writer);

        return $writer->getStream();
    }

    /**
     * @param Table\PostTable $postTable
     *
     * @return string
     */
    public function visitPostTable(Table\PostTable $postTable): string
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

        $postTable->getFormat()->accept($this->postFormatVisitor, $streamWriter);

        return $streamWriter->getStream();
    }

    /**
     * @param Table\RawTable $rawTable
     *
     * @return string
     */
    public function visitRawTable(Table\RawTable $rawTable): string
    {
        $writer = new StreamWriter();

        $writer->writeStream($rawTable->getContent());

        return $writer->getStream();
    }

    /**
     * @param Table\TableDirectoryEntry $tableDirectoryEntry
     *
     * @return string
     */
    public function visitTableDirectoryEntry(Table\TableDirectoryEntry $tableDirectoryEntry): string
    {
        $writer = new StreamWriter();

        $writer->writeTagFromString($tableDirectoryEntry->getTag());
        $writer->writeUInt32($tableDirectoryEntry->getCheckSum());
        $writer->writeOffset32($tableDirectoryEntry->getOffset());
        $writer->writeUInt32($tableDirectoryEntry->getLength());

        return $writer->getStream();
    }

    /**
     * @param Table\NameTable $nameTable
     *
     * @return string
     */
    public function visitNameTable(Table\NameTable $nameTable)
    {
        $writer = new StreamWriter();

        $writer->writeUInt16($nameTable->getFormat());
        $writer->writeUInt16($nameTable->getCount());
        $writer->writeOffset16($nameTable->getStringOffset());

        foreach ($nameTable->getNameRecords() as $nameRecord) {
            $writer->writeUInt16($nameRecord->getPlatformID());
            $writer->writeUInt16($nameRecord->getEncodingID());
            $writer->writeUInt16($nameRecord->getLanguageID());
            $writer->writeUInt16($nameRecord->getNameID());
            $writer->writeUInt16($nameRecord->getLength());
            $writer->writeOffset16($nameRecord->getOffset());
        }

        foreach ($nameTable->getNameRecords() as $nameRecord) {
            $writer->writeStream($nameRecord->getValue());
        }

        return $writer->getStream();
    }
}
