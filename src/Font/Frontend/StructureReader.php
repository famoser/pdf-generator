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

use PdfGenerator\Font\Frontend\Structure\FontDirectory;
use PdfGenerator\Font\Frontend\Structure\OffsetTable;
use PdfGenerator\Font\Frontend\Structure\Table\CMapTable;
use PdfGenerator\Font\Frontend\Structure\TableDirectoryEntry;

class StructureReader
{
    /**
     * @param FileReader $fileReader
     *
     * @throws \Exception
     *
     * @return FontDirectory
     */
    public function readFontDirectory(FileReader $fileReader)
    {
        $fontDirectory = new FontDirectory();

        $offsetTable = $this->readOffsetTable($fileReader);
        $fontDirectory->setOffsetTable($offsetTable);

        if (!$offsetTable->isTrueTypeFont()) {
            throw new \Exception('This font type is not supported: ' . $offsetTable->getScalerType());
        }

        for ($i = 0; $i < $offsetTable->getNumTables(); ++$i) {
            $tableDirectoryEntry = $this->readTableDirectoryEntry($fileReader);
            $fontDirectory->addTableDirectoryEntry($tableDirectoryEntry);
        }

        return $fontDirectory;
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
        $tableDirectoryEntry->setOffset($fileReader->readUInt32());
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
    public function readCMapTable(FileReader $fileReader)
    {
        $cmapTable = new CMapTable();

        $cmapTable->setVersion($fileReader->readUInt16());
        $cmapTable->setNumberSubtables($fileReader->readUInt16());

        return $cmapTable;
    }
}
