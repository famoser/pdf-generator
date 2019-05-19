<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Tests\Integration\Font\Frontend;

use PdfGenerator\Font\Frontend\FileReader;
use PdfGenerator\Font\Frontend\Structure\OffsetTable;
use PdfGenerator\Font\Frontend\Structure\Table\CMap\FormatReader;
use PdfGenerator\Font\Frontend\Structure\TableDirectoryEntry;
use PdfGenerator\Font\Frontend\StructureReader;
use PHPUnit\Framework\TestCase;

class StructureReaderTest extends TestCase
{
    private static $defaultFilePath = __DIR__ . \DIRECTORY_SEPARATOR . 'OpenSans-Regular.ttf';

    /**
     * @return FileReader
     */
    public static function getFileReader()
    {
        $content = file_get_contents(self::$defaultFilePath);

        return new FileReader($content);
    }

    /**
     * @return StructureReader
     */
    public static function getStructureReader()
    {
        $formatReader = new FormatReader();

        return new StructureReader($formatReader);
    }

    /**
     * @throws \Exception
     */
    public function testReadFontDirectory_fontDirectoryAsExpected()
    {
        // arrange
        $fileReader = self::getFileReader();
        $structureReader = self::getStructureReader();

        $fontDirectory = $structureReader->readFontDirectory($fileReader);

        // assert
        $this->assertOffsetTable($fontDirectory->getOffsetTable());
        $this->assertTableDirectoryEntries($fontDirectory->getTableDirectoryEntries());
    }

    /**
     * @throws \Exception
     */
    public function testReadCMapTable_cMapTableAsExpected()
    {
        // arrange
        $fileReader = self::getFileReader();
        $structureReader = self::getStructureReader();
        $this->setToTableLocation($fileReader, $structureReader, 'cmap');

        $cmapTable = $structureReader->readCMapTable($fileReader);

        // assert
        $this->assertTrue(true);
        var_dump($cmapTable);
    }

    /**
     * @param FileReader $fileReader
     * @param StructureReader $structureReader
     * @param string $tagName
     *
     * @throws \Exception
     */
    private function setToTableLocation(FileReader $fileReader, StructureReader $structureReader, string $tagName)
    {
        $fontDirectory = $structureReader->readFontDirectory($fileReader);

        foreach ($fontDirectory->getTableDirectoryEntries() as $tableDirectoryEntry) {
            if ($tableDirectoryEntry->getTag() === $tagName) {
                $fileReader->setOffset($tableDirectoryEntry->getOffset());
                var_dump('set offset to ' . $fileReader->getOffset());

                return;
            }
        }

        $this->fail('did not find the requested table in that font');
    }

    /**
     * @param OffsetTable $offsetTable
     */
    private function assertOffsetTable(OffsetTable $offsetTable)
    {
        $this->assertSame(65536, $offsetTable->getScalerType()); // hardcoded in our example font
        $this->assertSame(17, $offsetTable->getNumTables()); // hardcoded in our example font
        $this->assertSame(16 * 16, $offsetTable->getSearchRange()); // search 16 tables with binary tree; multiply by 16 because specification says so
        $this->assertSame(4, $offsetTable->getEntrySelector()); // how many levels deep the binary tree is
        $this->assertSame((17 - 16) * 16, $offsetTable->getRangeShift()); // 17 are number of tables; 16 are in binary tree; hence one is missed if not looking in binary tree
    }

    /**
     * @param TableDirectoryEntry[] $tableDirectoryEntries
     */
    private function assertTableDirectoryEntries(array $tableDirectoryEntries)
    {
        $this->assertCount(17, $tableDirectoryEntries);

        $firstEntry = $tableDirectoryEntries[0];
        $this->assertSame('GDEF', $firstEntry->getTag());
        $this->assertSame(95612, $firstEntry->getOffset());
        $this->assertSame(46, $firstEntry->getLength());
    }
}
