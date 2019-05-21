<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend;

use PdfGenerator\Font\Frontend\Structure\Font;
use PdfGenerator\Font\Frontend\Structure\Table\CMap\FormatReader;
use PdfGenerator\Font\Frontend\Structure\Table\CMap\Subtable;
use PdfGenerator\Font\Frontend\Structure\Table\CMapTable;
use PdfGenerator\Font\Frontend\Structure\Table\CvtTable;
use PdfGenerator\Font\Frontend\Structure\Table\FpgmTable;
use PdfGenerator\Font\Frontend\Structure\Table\GaspTable;
use PdfGenerator\Font\Frontend\Structure\Table\GlyfTable;
use PdfGenerator\Font\Frontend\Structure\Table\HeadTable;
use PdfGenerator\Font\Frontend\Structure\Table\HHeaTable;
use PdfGenerator\Font\Frontend\Structure\Table\HMtx\LongHorMetric;
use PdfGenerator\Font\Frontend\Structure\Table\HMtxTable;
use PdfGenerator\Font\Frontend\Structure\Table\LocaTable;
use PdfGenerator\Font\Frontend\Structure\Table\MaxPTable;
use PdfGenerator\Font\Frontend\Structure\Table\NameTable;
use PdfGenerator\Font\Frontend\Structure\Table\OffsetTable;
use PdfGenerator\Font\Frontend\Structure\Table\OS2Table;
use PdfGenerator\Font\Frontend\Structure\Table\PrepTable;
use PdfGenerator\Font\Frontend\Structure\Table\RawTable;
use PdfGenerator\Font\Frontend\Structure\Table\TableDirectoryEntry;
use PdfGenerator\Font\Frontend\Structure\Traits\RawContent;
use PdfGenerator\Font\Frontend\Structure\Traits\Reader;

class StructureReader
{
    /**
     * @var FormatReader
     */
    private $formatReader;

    /**
     * StructureReader constructor.
     *
     * @param FormatReader $formatReader
     */
    public function __construct(FormatReader $formatReader)
    {
        $this->formatReader = $formatReader;
    }

    /**
     * @param FileReader $fileReader
     *
     * @throws \Exception
     *
     * @return Font
     */
    public function readFont(FileReader $fileReader)
    {
        $font = new Font();

        $offsetTable = $this->readOffsetTable($fileReader);
        $font->setOffsetTable($offsetTable);

        if (!$offsetTable->isTrueTypeFont()) {
            throw new \Exception('This font type is not supported: ' . $offsetTable->getScalerType());
        }

        for ($i = 0; $i < $offsetTable->getNumTables(); ++$i) {
            $tableDirectoryEntry = $this->readTableDirectoryEntry($fileReader);
            $font->addTableDirectoryEntry($tableDirectoryEntry);
        }

        $this->readTables($fileReader, $font);

        return $font;
    }

    /**
     * @param FileReader $fileReader
     * @param Font $font
     *
     * @throws \Exception
     */
    private function readTables(FileReader $fileReader, Font $font)
    {
        /** @var TableDirectoryEntry $locaEntry */
        $locaEntry = null;
        /** @var TableDirectoryEntry $hmtxEntry */
        $hmtxEntry = null;
        /** @var TableDirectoryEntry $glyfEntry */
        $glyfEntry = null;

        foreach ($font->getTableDirectoryEntries() as $tableDirectoryEntry) {
            $fileReader->setOffset($tableDirectoryEntry->getOffset());

            switch ($tableDirectoryEntry->getTag()) {
                case 'cmap':
                    $table = $this->readCMapTable($fileReader);
                    $font->setCMapTable($table);
                    break;
                case 'maxp':
                    $table = $this->readMaxPTable($fileReader);
                    $font->setMaxPTable($table);
                    break;
                case 'head':
                    $table = $this->readHeadTable($fileReader);
                    $font->setHeadTable($table);
                    break;
                case 'OS/2':
                    $table = $this->readRawContentTable($fileReader, $tableDirectoryEntry->getLength(), new OS2Table());
                    $font->setOS2Table($table);
                    break;
                case 'name':
                    $table = $this->readRawContentTable($fileReader, $tableDirectoryEntry->getLength(), new NameTable());
                    $font->setNameTable($table);
                    break;
                case 'cvt ':
                    $table = $this->readRawContentTable($fileReader, $tableDirectoryEntry->getLength(), new CvtTable());
                    $font->setCvtTable($table);
                    break;
                case 'fpgm':
                    $table = $this->readRawContentTable($fileReader, $tableDirectoryEntry->getLength(), new FpgmTable());
                    $font->setFpqmTable($table);
                    break;
                case 'gasp':
                    $table = $this->readRawContentTable($fileReader, $tableDirectoryEntry->getLength(), new GaspTable());
                    $font->setGaspTable($table);
                    break;
                case 'prep':
                    $table = $this->readRawContentTable($fileReader, $tableDirectoryEntry->getLength(), new PrepTable());
                    $font->setPrepTable($table);
                    break;
                case 'hhea':
                    $table = $this->readHHeaTable($fileReader);
                    $font->setHHeaTable($table);
                    break;
                case 'loca':
                    $locaEntry = $tableDirectoryEntry;
                    break;
                case 'hmtx':
                    $hmtxEntry = $tableDirectoryEntry;
                    break;
                case 'glyf':
                    $glyfEntry = $tableDirectoryEntry;
                    break;
                default:
                    $table = $this->readRawTable($fileReader, $tableDirectoryEntry->getLength(), $tableDirectoryEntry->getTag());
                    $font->addRawTable($table);
                    break;
            }
        }

        if ($locaEntry !== null) {
            $fileReader->setOffset($locaEntry->getOffset());
            $table = $this->readLocaTable($fileReader, $font->getHeadTable(), $font->getMaxPTable());
            $font->setLocaTable($table);
        }

        if ($hmtxEntry !== null) {
            $fileReader->setOffset($hmtxEntry->getOffset());
            $table = $this->readHMtxTable($fileReader, $font->getHHeaTable(), $font->getMaxPTable());
            $font->setHMtxTable($table);
        }

        if ($glyfEntry !== null) {
            $fileReader->setOffset($glyfEntry->getOffset());
            $tables = $this->readGlyfTables($fileReader, $font->getLocaTable(), $font->getHeadTable());
            $font->setGlyfTables($tables);
        }
    }

    /**
     * @param FileReader $fileReader
     *
     * @throws \Exception
     *
     * @return OffsetTable
     */
    private function readOffsetTable(FileReader $fileReader)
    {
        $offsetTable = new OffsetTable();

        $offsetTable->setScalerType($fileReader->readUInt32());
        $offsetTable->setNumTables($fileReader->readUInt16());
        Reader::readBinaryTreeSearchableUInt16($fileReader, $offsetTable);

        return $offsetTable;
    }

    /**
     * @param FileReader $fileReader
     *
     * @throws \Exception
     *
     * @return TableDirectoryEntry
     */
    private function readTableDirectoryEntry(FileReader $fileReader)
    {
        $tableDirectoryEntry = new TableDirectoryEntry();

        $tableDirectoryEntry->setTag($fileReader->readTagAsString());
        $tableDirectoryEntry->setCheckSum($fileReader->readUInt32());
        $tableDirectoryEntry->setOffset($fileReader->readOffset32());
        $tableDirectoryEntry->setLength($fileReader->readUInt32());

        return $tableDirectoryEntry;
    }

    /**
     * @param FileReader $fileReader
     *
     * @throws \Exception
     *
     * @return CMapTable
     */
    private function readCMapTable(FileReader $fileReader)
    {
        $cmapTable = new CMapTable();

        $offset = $fileReader->getOffset();

        $cmapTable->setVersion($fileReader->readUInt16());
        $cmapTable->setNumberSubtables($fileReader->readUInt16());

        for ($i = 0; $i < $cmapTable->getNumberSubtables(); ++$i) {
            $subTable = $this->readCMapSubtable($fileReader, $offset);
            $cmapTable->addSubtable($subTable);
        }

        return $cmapTable;
    }

    /**
     * @param FileReader $fileReader
     * @param int $cmapTableOffset
     *
     * @throws \Exception
     *
     * @return Subtable
     */
    private function readCMapSubtable(FileReader $fileReader, int $cmapTableOffset)
    {
        $cMapSubtable = new Subtable();

        $cMapSubtable->setPlatformID($fileReader->readUInt16());
        $cMapSubtable->setPlatformSpecificID($fileReader->readUInt16());
        $cMapSubtable->setOffset($fileReader->readOffset32());

        $fileReader->pushOffset($cmapTableOffset + $cMapSubtable->getOffset());
        $format = $this->formatReader->readFormat($fileReader);
        $cMapSubtable->setFormat($format);
        $fileReader->popOffset();

        return $cMapSubtable;
    }

    /**
     * @param FileReader $fileReader
     * @param LocaTable $locaTable
     * @param HeadTable $headTable
     *
     * @throws \Exception
     *
     * @return GlyfTable[]
     */
    private function readGlyfTables(FileReader $fileReader, LocaTable $locaTable, HeadTable $headTable)
    {
        $glyphTableOffset = $fileReader->getOffset();
        // if short format the offsets are in words, else in bytes
        $offsetMultiplier = $headTable->getIndexToLocFormat() === 0 ? 2 : 1;

        $glyfTables = [];

        $glyphCount = \count($locaTable->getOffsets()) - 1;
        for ($i = 0; $i < $glyphCount; ++$i) {
            $startGlyphOffset = $locaTable->getOffsets()[$i] * $offsetMultiplier;
            $endGlyphOfOffset = $locaTable->getOffsets()[$i + 1] * $offsetMultiplier;

            // skip glyph construction if length is 0
            if ($startGlyphOffset === $endGlyphOfOffset) {
                $glyfTables[] = null;
                continue;
            }

            $fileReader->setOffset($startGlyphOffset + $glyphTableOffset);

            $glyfTable = new GlyfTable();
            $glyfTable->setNumberOfContours($fileReader->readInt16());
            Reader::readBoundingBoxFWORD($fileReader, $glyfTable);

            $rawFontData = $fileReader->readUntil($endGlyphOfOffset + $glyphTableOffset);
            $glyfTable->setContent($rawFontData);

            $glyfTables[] = $glyfTable;
        }

        return $glyfTables;
    }

    /**
     * @param FileReader $fileReader
     * @param HeadTable $headTable
     * @param MaxPTable $maxPTable
     *
     * @throws \Exception
     *
     * @return LocaTable
     */
    private function readLocaTable(FileReader $fileReader, HeadTable $headTable, MaxPTable $maxPTable)
    {
        $glyfTable = new LocaTable();

        $numberOfGlyphs = $maxPTable->getNumGlyphs() + 1;

        if ($headTable->getIndexToLocFormat() === 0) {
            $offsets = $fileReader->readOffset16Array($numberOfGlyphs);
        } else {
            $offsets = $fileReader->readOffset32Array($numberOfGlyphs);
        }

        $glyfTable->setOffsets($offsets);

        return $glyfTable;
    }

    /**
     * @param FileReader $fileReader
     *
     * @throws \Exception
     *
     * @return MaxPTable
     */
    private function readMaxPTable(FileReader $fileReader)
    {
        $maxPTable = new MaxPTable();

        $maxPTable->setVersion($fileReader->readFixed());
        $maxPTable->setNumGlyphs($fileReader->readUInt16());
        $maxPTable->setMaxPoints($fileReader->readUInt16());
        $maxPTable->setMaxContours($fileReader->readUInt16());
        $maxPTable->setMaxCompositePoints($fileReader->readUInt16());
        $maxPTable->setMaxCompositeContours($fileReader->readUInt16());
        $maxPTable->setMaxZones($fileReader->readUInt16());
        $maxPTable->setMaxTwilightPoints($fileReader->readUInt16());
        $maxPTable->setMaxStorage($fileReader->readUInt16());
        $maxPTable->setMaxFunctionDefs($fileReader->readUInt16());
        $maxPTable->setMaxInstructionDefs($fileReader->readUInt16());
        $maxPTable->setMaxStackElements($fileReader->readUInt16());
        $maxPTable->setMaxSizeOfInstructions($fileReader->readUInt16());
        $maxPTable->setMaxComponentElements($fileReader->readUInt16());
        $maxPTable->setMaxComponentDepth($fileReader->readUInt16());

        return $maxPTable;
    }

    /**
     * @param FileReader $fileReader
     *
     * @throws \Exception
     *
     * @return HeadTable
     */
    private function readHeadTable(FileReader $fileReader)
    {
        $headTable = new HeadTable();

        $headTable->setMajorVersion($fileReader->readUInt16());
        $headTable->setMinorVersion($fileReader->readUInt16());
        $headTable->setFontRevision($fileReader->readFixed());
        $headTable->setCheckSumAdjustment($fileReader->readUInt32());
        $headTable->setMagicNumber($fileReader->readUInt32());
        $headTable->setFlags($fileReader->readUInt16());
        $headTable->setUnitsPerEm($fileReader->readUInt16());
        $headTable->setCreated($fileReader->readLONGDATETIME());
        $headTable->setModified($fileReader->readLONGDATETIME());
        Reader::readBoundingBoxInt16($fileReader, $headTable);
        $headTable->setMacStyle($fileReader->readUInt16());
        $headTable->setLowestRecPPEM($fileReader->readUInt16());
        $headTable->setFontDirectionHints($fileReader->readInt16());
        $headTable->setIndexToLocFormat($fileReader->readInt16());
        $headTable->setGlyphDataFormat($fileReader->readInt16());

        return $headTable;
    }

    /**
     * @param FileReader $fileReader
     * @param int $size
     * @param RawContent $targetTable
     *
     * @return RawContent|OS2Table|NameTable|CvtTable|FpgmTable|GaspTable|PrepTable
     */
    private function readRawContentTable(FileReader $fileReader, int $size, $targetTable)
    {
        $endOffset = $fileReader->getOffset() + $size;

        $targetTable->setContent($fileReader->readUntil($endOffset));

        return $targetTable;
    }

    /**
     * @param FileReader $fileReader
     * @param int $size
     * @param string $tag
     *
     * @return RawTable|OS2Table|NameTable
     */
    private function readRawTable(FileReader $fileReader, int $size, string $tag)
    {
        $rawTable = new RawTable();
        $rawTable->setTag($tag);

        $endOffset = $fileReader->getOffset() + $size;
        $rawTable->setContent($fileReader->readUntil($endOffset));

        return $rawTable;
    }

    /**
     * @param FileReader $fileReader
     *
     * @throws \Exception
     *
     * @return HHeaTable
     */
    private function readHHeaTable(FileReader $fileReader)
    {
        $table = new HHeaTable();

        $table->setVersion($fileReader->readFixed());
        $table->setAscent($fileReader->readFWORD());
        $table->setDecent($fileReader->readFWORD());
        $table->setLineGap($fileReader->readFWORD());
        $table->setAdvanceWidthMax($fileReader->readUFWORD());
        $table->setMinLeftSideBearing($fileReader->readFWORD());
        $table->setMinRightSideBearing($fileReader->readFWORD());
        $table->setXMaxExtent($fileReader->readFWORD());
        $table->setCaretSlopeRise($fileReader->readInt16());
        $table->setCaretSlopeRun($fileReader->readInt16());
        $table->setCaretOffset($fileReader->readFWORD());

        // skip resevred characters
        $fileReader->readInt32();
        $fileReader->readInt32();

        $table->setMetricDataFormat($fileReader->readInt16());
        $table->setNumOfLongHorMetrics($fileReader->readUInt16());

        return $table;
    }

    /**
     * @param FileReader $fileReader
     * @param HHeaTable $hHeaTable
     * @param MaxPTable $maxPTable
     *
     * @throws \Exception
     *
     * @return HMtxTable
     */
    private function readHMtxTable(FileReader $fileReader, HHeaTable $hHeaTable, MaxPTable $maxPTable)
    {
        $hMtxTable = new HMtxTable();

        for ($i = 0; $i < $hHeaTable->getNumOfLongHorMetrics(); ++$i) {
            $longHorMetric = new LongHorMetric();
            $longHorMetric->setAdvanceWidth($fileReader->readUInt16());
            $longHorMetric->setLeftSideBearing($fileReader->readInt16());

            $hMtxTable->addLongHorMetric($longHorMetric);
        }

        $leftSideBearingEntriesCount = $maxPTable->getNumGlyphs() - $hHeaTable->getNumOfLongHorMetrics();
        for ($i = 0; $i < $leftSideBearingEntriesCount; ++$i) {
            $hMtxTable->addLeftSideBearing($fileReader->readFWORD());
        }

        return $hMtxTable;
    }
}
