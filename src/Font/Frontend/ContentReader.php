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

use PdfGenerator\Font\Frontend\Content\Font;
use PdfGenerator\Font\Frontend\Content\TableDirectory;
use PdfGenerator\Font\Frontend\Structure\TableDirectoryEntry;

class ContentReader
{
    /**
     * @var StructureReader
     */
    private $structureReader;

    /**
     * StructureReader constructor.
     *
     * @param StructureReader $structureReader
     */
    public function __construct(StructureReader $structureReader)
    {
        $this->structureReader = $structureReader;
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
        $fontDirectory = $this->structureReader->readFontDirectory($fileReader);

        var_dump($fontDirectory);
        var_dump($fileReader->getOffset());
        $font = new Font();
        $font->setTableDirectory($this->readTables($fileReader, $fontDirectory->getTableDirectoryEntries()));

        return $font;
    }

    /**
     * @param FileReader $fileReader
     * @param TableDirectoryEntry[] $tableDirectoryEntries
     *
     * @throws \Exception
     *
     * @return TableDirectory
     */
    private function readTables(FileReader $fileReader, array $tableDirectoryEntries): TableDirectory
    {
        $tableDirectory = new TableDirectory();

        foreach ($tableDirectoryEntries as $tableDirectoryEntry) {
            $fileReader->setOffset($tableDirectoryEntry->getOffset());
            switch ($tableDirectoryEntry->getTag()) {
                case 'cmap':
                    $cmapTable = $this->structureReader->readCMapTable($fileReader);
                    $cmapFormatDirectory = $this->readCMapFormatTables($fileReader, $cmapTable->getNumberSubtables());
                    $tableDirectory->setCmapFormatDirectory($cmapFormatDirectory);
                    break;
            }
        }

        return $tableDirectory;
    }
}
