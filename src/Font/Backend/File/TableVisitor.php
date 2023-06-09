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
    private FormatVisitor $cMapFormatVisitor;

    private Table\Post\FormatVisitor $postFormatVisitor;

    public static int $indexToLocFormat = 0;

    /**
     * TableWriter constructor.
     */
    public function __construct(FormatVisitor $cMapFormatVisitor, Table\Post\FormatVisitor $postFormatVisitor)
    {
        $this->cMapFormatVisitor = $cMapFormatVisitor;
        $this->postFormatVisitor = $postFormatVisitor;
    }

    public static function create(): TableVisitor
    {
        $cmapFormatVisitor = new FormatVisitor();
        $postFormatVisitor = new Table\Post\FormatVisitor();

        return new self($cmapFormatVisitor, $postFormatVisitor);
    }

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
        $subtableCount = \count($cMapTable->getSubtables());
        $subtableOffset = $subtableCount * $subtableSize;
        $formatOffset = $cMapOffset + $subtableOffset;
        for ($i = 0; $i < $subtableCount; ++$i) {
            $formatOffset += $offsetBySubtable[$i];
            $subTable = $cMapTable->getSubtables()[$i];

            $writer->writeUInt16($subTable->getPlatformID());
            $writer->writeUInt16($subTable->getPlatformSpecificID());
            $writer->writeOffset32($formatOffset);
        }

        $writer->writeStream($formatStreamWriter->getStream());

        return $writer->getStream();
    }

    public function visitGlyfTable(Table\GlyfTable $glyfTable): string
    {
        $writer = new StreamWriter();

        $writer->writeInt16($glyfTable->getNumberOfContours());
        Writer::writeBoundingBoxFWORD($glyfTable, $writer);

        foreach ($glyfTable->getComponentGlyphs() as $componentGlyph) {
            $writer->writeUInt16($componentGlyph->getFlags());
            $writer->writeUInt16($componentGlyph->getGlyphIndex());
            $writer->writeNullableStream($componentGlyph->getContent());
        }

        $writer->writeNullableStream($glyfTable->getContent());

        return $writer->getStream();
    }

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

    public function visitHHeaTable(Table\HHeaTable $hHeaTable): string
    {
        $writer = new StreamWriter();

        $writer->writeFixed($hHeaTable->getVersion());
        $writer->writeFWORD($hHeaTable->getAscent());
        $writer->writeFWORD($hHeaTable->getDescent());
        $writer->writeFWORD($hHeaTable->getLineGap());
        $writer->writeUFWORD($hHeaTable->getAdvanceWidthMax());
        $writer->writeFWORD($hHeaTable->getMinLeftSideBearing());
        $writer->writeFWORD($hHeaTable->getMinRightSideBearing());
        $writer->writeFWORD($hHeaTable->getXMaxExtent());
        $writer->writeInt16($hHeaTable->getCaretSlopeRise());
        $writer->writeInt16($hHeaTable->getCaretSlopeRun());
        $writer->writeInt16($hHeaTable->getCaretOffset());

        // skip reserved characters
        $writer->writeUInt32(0);
        $writer->writeUInt32(0);

        $writer->writeInt16($hHeaTable->getMetricDataFormat());
        $writer->writeUInt16($hHeaTable->getNumOfLongHorMetrics());

        return $writer->getStream();
    }

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

    public function visitLocaTable(Table\LocaTable $locaTable): string
    {
        $writer = new StreamWriter();

        if (0 === self::$indexToLocFormat) {
            $writer->writeOffset16Array($locaTable->getOffsets());
        } else {
            $writer->writeOffset32Array($locaTable->getOffsets());
        }

        return $writer->getStream();
    }

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

    public function visitOffsetTable(Table\OffsetTable $offsetTable): string
    {
        $writer = new StreamWriter();

        $writer->writeUInt32($offsetTable->getScalerType());
        $writer->writeUInt16($offsetTable->getNumTables());
        Writer::writeBinaryTreeSearchableUInt16($offsetTable, $writer);

        return $writer->getStream();
    }

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

    public function visitRawTable(Table\RawTable $rawTable): string
    {
        $writer = new StreamWriter();

        $writer->writeStream($rawTable->getContent());

        return $writer->getStream();
    }

    public function visitTableDirectoryEntry(Table\TableDirectoryEntry $tableDirectoryEntry): string
    {
        $writer = new StreamWriter();

        $writer->writeTagFromString($tableDirectoryEntry->getTag());
        $writer->writeUInt32($tableDirectoryEntry->getCheckSum());
        $writer->writeOffset32($tableDirectoryEntry->getOffset());
        $writer->writeUInt32($tableDirectoryEntry->getLength());

        return $writer->getStream();
    }

    public function visitNameTable(Table\NameTable $nameTable): string
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

    public function visitOS2Table(Table\OS2Table $os2Table)
    {
        $writer = new StreamWriter();

        $writer->writeUInt16($os2Table->getVersion());

        $writer->writeInt16($os2Table->getXAvgCharWidth());

        $writer->writeUInt16($os2Table->getUsWeightClass());
        $writer->writeUInt16($os2Table->getUsWidthClass());

        $writer->writeUInt16($os2Table->getFsType());

        $writer->writeInt16($os2Table->getYSubscriptXSize());
        $writer->writeInt16($os2Table->getYSubscriptYSize());
        $writer->writeInt16($os2Table->getYSubscriptXOffset());
        $writer->writeInt16($os2Table->getYSubscriptYOffset());

        $writer->writeInt16($os2Table->getYSuperscriptXSize());
        $writer->writeInt16($os2Table->getYSuperscriptYSize());
        $writer->writeInt16($os2Table->getYSuperscriptXOffset());
        $writer->writeInt16($os2Table->getYSuperscriptYOffset());

        $writer->writeInt16($os2Table->getYStrikeoutSize());
        $writer->writeInt16($os2Table->getYStrikeoutPosition());
        $writer->writeInt16($os2Table->getSFamilyClass());

        $writer->writeUInt8Array($os2Table->getPanose());

        $writer->writeUInt32Array($os2Table->getUlUnicodeRanges());

        $writer->writeTagFromString($os2Table->getAchVendID());

        $writer->writeUInt16($os2Table->getFsSelection());

        $writer->writeUInt16($os2Table->getUsFirstCharIndex());
        $writer->writeUInt16($os2Table->getUsLastCharIndex());

        $writer->writeInt16($os2Table->getSTypoAscender());
        $writer->writeInt16($os2Table->getSTypoDecender());
        $writer->writeInt16($os2Table->getSTypoLineGap());

        $writer->writeUInt16($os2Table->getUsWinAscent());
        $writer->writeUInt16($os2Table->getUsWinDecent());

        if ($os2Table->getVersion() <= 0) {
            return $writer->getStream();
        }

        $writer->writeUInt32Array($os2Table->getUlCodePageRanges());

        if ($os2Table->getVersion() <= 3) {
            return $writer->getStream();
        }

        $writer->writeInt16($os2Table->getSxHeight());
        $writer->writeInt16($os2Table->getSCapHeight());
        $writer->writeUInt16($os2Table->getUsDefaultChar());
        $writer->writeUInt16($os2Table->getUsBreakChar());
        $writer->writeUInt16($os2Table->getUsMaxContext());

        if (4 === $os2Table->getVersion()) {
            return $writer->getStream();
        }

        $writer->writeUInt16($os2Table->getUsLowerOptimalPointSize());
        $writer->writeUInt16($os2Table->getUsUpperOptimalPointSize());

        return $writer->getStream();
    }
}
