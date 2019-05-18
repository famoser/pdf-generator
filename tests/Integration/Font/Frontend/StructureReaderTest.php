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
use PdfGenerator\Font\Frontend\StructureReader;
use PHPUnit\Framework\TestCase;

class StructureReaderTest extends TestCase
{
    private static $defaultFilePath = __DIR__ . \DIRECTORY_SEPARATOR . 'OpenSans-Regular.ttf';

    /**
     * @return FileReader
     */
    private static function getFileReader()
    {
        $content = file_get_contents(self::$defaultFilePath);

        return new FileReader($content);
    }

    /**
     * @throws \Exception
     */
    public function testReadFontDirectory_offsetTableAsExpected()
    {
        // arrange
        $fileReader = $this->getFileReader();
        $structureReader = new StructureReader();

        $fontDirectory = $structureReader->readFontDirectory($fileReader);

        // assert
        $this->assertOffsetTable($fontDirectory->getOffsetTable());
    }

    private function assertOffsetTable(OffsetTable $offsetTable)
    {
        $this->assertSame(65536, $offsetTable->getScalerType()); // hardcoded in our example font
        $this->assertSame(17, $offsetTable->getNumTables()); // hardcoded in our example font
        $this->assertSame(16 * 16, $offsetTable->getSearchRange()); // search 16 tables with binary tree; multiply by 16 because specification says so
        $this->assertSame(4, $offsetTable->getEntrySelector()); // how many levels deep the binary tree is
        $this->assertSame((17 - 16) * 16, $offsetTable->getRangeShift()); // 17 are number of tables; 16 are in binary tree; hence one is missed if not looking in binary tree
    }
}
