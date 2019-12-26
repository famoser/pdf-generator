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

use PdfGenerator\Font\Frontend\File\FontFile;
use PdfGenerator\Font\Frontend\File\Table\CMap\FormatReader;
use PdfGenerator\Font\Frontend\File\Table\CMap\Subtable;
use PdfGenerator\Font\Frontend\File\Table\CMapTable;
use PdfGenerator\Font\Frontend\File\Table\GlyfTable;
use PdfGenerator\Font\Frontend\File\Table\HeadTable;
use PdfGenerator\Font\Frontend\File\Table\HHeaTable;
use PdfGenerator\Font\Frontend\File\Table\HMtx\LongHorMetric;
use PdfGenerator\Font\Frontend\File\Table\HMtxTable;
use PdfGenerator\Font\Frontend\File\Table\LocaTable;
use PdfGenerator\Font\Frontend\File\Table\MaxPTable;
use PdfGenerator\Font\Frontend\File\Table\Name\LangTagRecord;
use PdfGenerator\Font\Frontend\File\Table\Name\NameRecord;
use PdfGenerator\Font\Frontend\File\Table\NameTable;
use PdfGenerator\Font\Frontend\File\Table\OffsetTable;
use PdfGenerator\Font\Frontend\File\Table\OS2Table;
use PdfGenerator\Font\Frontend\File\Table\PostTable;
use PdfGenerator\Font\Frontend\File\Table\RawTable;
use PdfGenerator\Font\Frontend\File\Table\TableDirectoryEntry;
use PdfGenerator\Font\Frontend\File\Traits\Reader;

class FileReader
{
    /**
     * @var FormatReader
     */
    private $cMapFormatReader;

    /**
     * @var File\Table\Post\FormatReader
     */
    private $postFormatReader;

    /**
     * StructureReader constructor.
     */
    public function __construct(FormatReader $cMapFormatReader, File\Table\Post\FormatReader $postFormatReader)
    {
        $this->cMapFormatReader = $cMapFormatReader;
        $this->postFormatReader = $postFormatReader;
    }

    /**
     * @throws \Exception
     *
     * @return FontFile
     */
    public function read(StreamReader $fileReader)
    {
        $offsetTable = $this->readOffsetTable($fileReader);

        if (!$offsetTable->isTrueTypeFont()) {
            throw new \Exception('This font type is not supported: ' . $offsetTable->getScalerType());
        }

        $tableDirectoryEntries = [];
        for ($i = 0; $i < $offsetTable->getNumTables(); ++$i) {
            $tableDirectoryEntries[] = $this->readTableDirectoryEntry($fileReader);
        }

        return $this->readFontFile($fileReader, $tableDirectoryEntries);
    }

    /**
     * @param TableDirectoryEntry[] $tableDirectoryEntries
     *
     * @throws \Exception
     */
    private function readFontFile(StreamReader $fileReader, array $tableDirectoryEntries): FontFile
    {
        $font = new FontFile();

        /** @var TableDirectoryEntry $locaEntry */
        $locaEntry = null;
        /** @var TableDirectoryEntry $hmtxEntry */
        $hmtxEntry = null;
        /** @var TableDirectoryEntry $glyfEntry */
        $glyfEntry = null;

        foreach ($tableDirectoryEntries as $tableDirectoryEntry) {
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
                case 'OS/2': // sizing infos used only by windows
                    $table = $this->readOS2Table($fileReader);
                    $font->setOS2Table($table);
                    break;
                case 'name':
                    $table = $this->readNameTable($fileReader);
                    $font->setNameTable($table);
                    break;
                case 'cvt ': // list of instructions
                    $table = $this->readRawTable($fileReader, $tableDirectoryEntry);
                    $font->setCvtTable($table);
                    break;
                case 'fpgm': // font program before font is used
                    $table = $this->readRawTable($fileReader, $tableDirectoryEntry);
                    $font->setFpgmTable($table);
                    break;
                case 'gasp': // grid-fitting rasterization properties
                    $table = $this->readRawTable($fileReader, $tableDirectoryEntry);
                    $font->setGaspTable($table);
                    break;
                case 'prep': // font program before glyph is printed
                    $table = $this->readRawTable($fileReader, $tableDirectoryEntry);
                    $font->setPrepTable($table);
                    break;
                case 'post':
                    $table = $this->readPostTable($fileReader, $tableDirectoryEntry->getLength());
                    $font->setPostTable($table);
                    break;
                case 'GDEF': // emoji things 1
                    $table = $this->readRawTable($fileReader, $tableDirectoryEntry);
                    $font->setGDEFTable($table);
                    break;
                case 'GPOS': // emoji things 2
                    $table = $this->readRawTable($fileReader, $tableDirectoryEntry);
                    $font->setGPOSTable($table);
                    break;
                case 'GSUB': // emoji things 3
                    $table = $this->readRawTable($fileReader, $tableDirectoryEntry);
                    $font->setGSUBTable($table);
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
                    $table = $this->readRawTable($fileReader, $tableDirectoryEntry);
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

        return $font;
    }

    /**
     * @throws \Exception
     *
     * @return OffsetTable
     */
    private function readOffsetTable(StreamReader $fileReader)
    {
        $offsetTable = new OffsetTable();

        $offsetTable->setScalerType($fileReader->readUInt32());
        $offsetTable->setNumTables($fileReader->readUInt16());
        Reader::readBinaryTreeSearchableUInt16($fileReader, $offsetTable);

        return $offsetTable;
    }

    /**
     * @throws \Exception
     *
     * @return TableDirectoryEntry
     */
    private function readTableDirectoryEntry(StreamReader $fileReader)
    {
        $tableDirectoryEntry = new TableDirectoryEntry();

        $tableDirectoryEntry->setTag($fileReader->readTagAsString());
        $tableDirectoryEntry->setCheckSum($fileReader->readUInt32());
        $tableDirectoryEntry->setOffset($fileReader->readOffset32());
        $tableDirectoryEntry->setLength($fileReader->readUInt32());

        return $tableDirectoryEntry;
    }

    /**
     * @throws \Exception
     *
     * @return CMapTable
     */
    private function readCMapTable(StreamReader $fileReader)
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
     * @throws \Exception
     *
     * @return Subtable
     */
    private function readCMapSubtable(StreamReader $fileReader, int $cmapTableOffset)
    {
        $cMapSubtable = new Subtable();

        $cMapSubtable->setPlatformID($fileReader->readUInt16());
        $cMapSubtable->setPlatformSpecificID($fileReader->readUInt16());
        $cMapSubtable->setOffset($fileReader->readOffset32());

        $fileReader->pushOffset($cmapTableOffset + $cMapSubtable->getOffset());
        $format = $this->cMapFormatReader->readFormat($fileReader);
        $cMapSubtable->setFormat($format);
        $fileReader->popOffset();

        return $cMapSubtable;
    }

    /**
     * @throws \Exception
     *
     * @return GlyfTable[]
     */
    private function readGlyfTables(StreamReader $fileReader, LocaTable $locaTable, HeadTable $headTable)
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
     * @throws \Exception
     *
     * @return LocaTable
     */
    private function readLocaTable(StreamReader $fileReader, HeadTable $headTable, MaxPTable $maxPTable)
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
     * @throws \Exception
     *
     * @return MaxPTable
     */
    private function readMaxPTable(StreamReader $fileReader)
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
     * @throws \Exception
     *
     * @return HeadTable
     */
    private function readHeadTable(StreamReader $fileReader)
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
     * @return RawTable
     */
    private function readRawTable(StreamReader $fileReader, TableDirectoryEntry $tableDirectoryEntry)
    {
        $rawTable = new RawTable();
        $rawTable->setTag($tableDirectoryEntry->getTag());

        $endOffset = $fileReader->getOffset() + $tableDirectoryEntry->getLength();
        $rawTable->setContent($fileReader->readUntil($endOffset));

        return $rawTable;
    }

    /**
     * @throws \Exception
     *
     * @return HHeaTable
     */
    private function readHHeaTable(StreamReader $fileReader)
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
        $table->setCaretOffset($fileReader->readInt16());

        // skip reserved characters
        $fileReader->readInt32();
        $fileReader->readInt32();

        $table->setMetricDataFormat($fileReader->readInt16());
        $table->setNumOfLongHorMetrics($fileReader->readUInt16());

        return $table;
    }

    /**
     * @throws \Exception
     *
     * @return PostTable
     */
    private function readPostTable(StreamReader $fileReader, int $length)
    {
        $table = new PostTable();

        $table->setVersion($fileReader->readFixed());
        $table->setItalicAngle($fileReader->readFixed());
        $table->setUnderlinePosition($fileReader->readFWORD());
        $table->setUnderlineThickness($fileReader->readFWORD());

        $table->setIsFixedPitch($fileReader->readUInt32());
        $table->setMinMemType42($fileReader->readUInt32());
        $table->setMaxMemType42($fileReader->readUInt32());
        $table->setMinMemType1($fileReader->readUInt32());
        $table->setMaxMemType1($fileReader->readUInt32());

        $remainingLength = $length - (2 * 4 + 2 * 2 + 5 * 4);
        $table->setFormat($this->postFormatReader->readFormat($fileReader, $table->getVersion(), $remainingLength));

        return $table;
    }

    /**
     * @throws \Exception
     *
     * @return NameTable
     */
    private function readNameTable(StreamReader $fileReader)
    {
        $startTableOffset = $fileReader->getOffset();

        $table = new NameTable();

        $table->setFormat($fileReader->readUInt16());
        $table->setCount($fileReader->readUInt16());
        $table->setStringOffset($fileReader->readOffset16());

        for ($i = 0; $i < $table->getCount(); ++$i) {
            $table->addNameRecord($this->readNameRecord($fileReader));
        }

        if ($table->getFormat() === 1) {
            $table->setLangTagCount($fileReader->readUInt16());

            for ($i = 0; $i < $table->getLangTagCount(); ++$i) {
                $table->addLangTagRecord($this->readLangTagRecord($fileReader));
            }
        }

        $stringOffset = $startTableOffset + $table->getStringOffset();
        $fileReader->setOffset($stringOffset);

        foreach ($table->getNameRecords() as $nameRecord) {
            $fileReader->setOffset($stringOffset + $nameRecord->getOffset());

            /*
            one could decode the rawValue, but
             - some encodings unclear from the TTF specification (unicode encoding = UTF-16?)
             - some encodings in the standard not implemented in php
            better to just push around the raw value for now
            */
            $rawValue = $fileReader->readFor($nameRecord->getLength());
            $nameRecord->setValue($rawValue);
        }

        foreach ($table->getLangTagRecords() as $langTagRecord) {
            $fileReader->setOffset($stringOffset + $langTagRecord->getOffset());
            $langTagRecord->setValue($fileReader->readFor($langTagRecord->getLength()));
        }

        return $table;
    }

    /**
     * @throws \Exception
     *
     * @return NameRecord
     */
    private function readNameRecord(StreamReader $streamReader)
    {
        $nameRecord = new NameRecord();

        $nameRecord->setPlatformID($streamReader->readUInt16());
        $nameRecord->setEncodingID($streamReader->readUInt16());
        $nameRecord->setLanguageID($streamReader->readUInt16());
        $nameRecord->setNameID($streamReader->readUInt16());
        $nameRecord->setLength($streamReader->readUInt16());
        $nameRecord->setOffset($streamReader->readOffset16());

        return $nameRecord;
    }

    /**
     * @throws \Exception
     *
     * @return OS2Table
     */
    private function readOS2Table(StreamReader $streamReader)
    {
        $os2Table = new OS2Table();

        $os2Table->setVersion($streamReader->readUInt16());

        $os2Table->setXAvgCharWidth($streamReader->readInt16());

        $os2Table->setUsWeightClass($streamReader->readUInt16());
        $os2Table->setUsWidthClass($streamReader->readUInt16());

        $os2Table->setFsType($streamReader->readUInt16());

        $os2Table->setYSubscriptXSize($streamReader->readInt16());
        $os2Table->setYSubscriptYSize($streamReader->readInt16());
        $os2Table->setYSubscriptXOffset($streamReader->readInt16());
        $os2Table->setYSubscriptYOffset($streamReader->readInt16());

        $os2Table->setYSuperscriptXSize($streamReader->readInt16());
        $os2Table->setYSuperscriptYSize($streamReader->readInt16());
        $os2Table->setYSuperscriptXOffset($streamReader->readInt16());
        $os2Table->setYSuperscriptYOffset($streamReader->readInt16());

        $os2Table->setYStrikeoutSize($streamReader->readInt16());
        $os2Table->setYStrikeoutPosition($streamReader->readInt16());
        $os2Table->setSFamilyClass($streamReader->readInt16());

        $os2Table->setPanose($streamReader->readUInt8Array(10));

        $os2Table->setUlUnicodeRanges($streamReader->readUInt32Array(4));

        $os2Table->setAchVendID($streamReader->readTagAsString());

        $os2Table->setFsSelection($streamReader->readUInt16());

        $os2Table->setUsFirstCharIndex($streamReader->readUInt16());
        $os2Table->setUsLastCharIndex($streamReader->readUInt16());

        $os2Table->setSTypoAscender($streamReader->readInt16());
        $os2Table->setSTypoDecender($streamReader->readInt16());
        $os2Table->setSTypoLineGap($streamReader->readInt16());

        $os2Table->setUsWinAscent($streamReader->readUInt16());
        $os2Table->setUsWinDecent($streamReader->readUInt16());

        if ($os2Table->getVersion() <= 0) {
            return $os2Table;
        }

        $os2Table->setUlCodePageRanges($streamReader->readUInt32Array(2));

        if ($os2Table->getVersion() <= 3) {
            return $os2Table;
        }

        $os2Table->setSxHeight($streamReader->readInt16());
        $os2Table->setSCapHeight($streamReader->readInt16());
        $os2Table->setUsDefaultChar($streamReader->readUInt16());
        $os2Table->setUsBreakChar($streamReader->readUInt16());
        $os2Table->setUsMaxContext($streamReader->readUInt16());

        if ($os2Table->getVersion() === 4) {
            return $os2Table;
        }

        $os2Table->setUsLowerOptimalPointSize($streamReader->readUInt16());
        $os2Table->setUsUpperOptimalPointSize($streamReader->readUInt16());

        return $os2Table;
    }

    /**
     * @throws \Exception
     *
     * @return LangTagRecord
     */
    private function readLangTagRecord(StreamReader $streamReader)
    {
        $langTagRecord = new LangTagRecord();

        $langTagRecord->setLength($streamReader->readUInt16());
        $langTagRecord->setOffset($streamReader->readOffset16());

        return $langTagRecord;
    }

    /**
     * @throws \Exception
     *
     * @return HMtxTable
     */
    private function readHMtxTable(StreamReader $fileReader, HHeaTable $hHeaTable, MaxPTable $maxPTable)
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
