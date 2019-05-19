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

use PdfGenerator\Font\Frontend\Content\CMapFormatDirectory;
use PdfGenerator\Font\Frontend\Content\Font;
use PdfGenerator\Font\Frontend\Content\TableDirectory;
use PdfGenerator\Font\Frontend\Structure\CMapFormatReader;
use PdfGenerator\Font\Frontend\Structure\TableDirectoryEntry;

class ContentReader
{
    /**
     * @var CMapFormatReader
     */
    private $cMapFormatReader;

    /**
     * @var StructureReader
     */
    private $structureReader;

    /**
     * StructureReader constructor.
     *
     * @param CMapFormatReader $cMapFormatReader
     * @param StructureReader $structureReader
     */
    public function __construct(CMapFormatReader $cMapFormatReader, StructureReader $structureReader)
    {
        $this->cMapFormatReader = $cMapFormatReader;
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
                    $cmapSubTable = $this->structureReader->readCMapSubtables($fileReader, $cmapTable->getNumberSubtables());
                    $cmapFormatDirectory = $this->readCMapFormatTables($fileReader, $cmapTable->getNumberSubtables());
                    $tableDirectory->setCmapFormatDirectory($cmapFormatDirectory);
                    break;
            }
        }

        return $tableDirectory;
    }

    /**
     * @param FileReader $fileReader
     * @param int $count
     *
     * @throws \Exception
     *
     * @return CMapFormatDirectory
     */
    private function readCMapFormatTables(FileReader $fileReader, int $count)
    {
        $cmapFormatDirectory = new CMapFormatDirectory();

        for ($i = 0; $i < $count; ++$i) {
            $startOffset = $fileReader->getOffset();
            $format = $fileReader->readUInt16();
            switch ($format) {
                case 4:
                    $cmapFormatDirectory->setFormat4($this->cMapFormatReader->readFormat4($fileReader, $startOffset));
                    break;
            }
            var_dump('format: ' . $format);
        }

        return $cmapFormatDirectory;
    }
}
