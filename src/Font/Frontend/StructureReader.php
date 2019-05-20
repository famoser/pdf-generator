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
use PdfGenerator\Font\Frontend\Structure\Table\OffsetTable;
use PdfGenerator\Font\Frontend\Structure\Table\TableDirectoryEntry;

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
        foreach ($font->getTableDirectoryEntries() as $tableDirectoryEntry) {
            $fileReader->setOffset($tableDirectoryEntry->getOffset());
            switch ($tableDirectoryEntry->getTag()) {
                case 'cmap':
                    $cmapTable = $this->readCMapTable($fileReader);
                    $font->setCMapTable($cmapTable);
                    break;
            }
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
        $offsetTable->setSearchRange($fileReader->readUInt16());
        $offsetTable->setEntrySelector($fileReader->readUInt16());
        $offsetTable->setRangeShift($fileReader->readUInt16());

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
}
