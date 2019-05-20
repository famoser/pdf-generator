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
use PdfGenerator\Font\Frontend\Structure\Table\CMap\Format\Format4;
use PdfGenerator\Font\Frontend\Structure\Table\CMap\FormatReader;
use PdfGenerator\Font\Frontend\Structure\Table\CMapTable;
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
        $this->assertCMapTable($cmapTable);
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

    /**
     * @param CMapTable $cmapTable
     */
    private function assertCMapTable(CMapTable $cmapTable)
    {
        $this->assertSame(0, $cmapTable->getVersion());
        $this->assertSame(1, $cmapTable->getNumberSubtables());
        $this->assertCount(1, $cmapTable->getSubtables());

        $format4Subtable = $cmapTable->getSubtables()[0];
        $this->assertSame(3, $format4Subtable->getPlatformID());
        $this->assertSame(1, $format4Subtable->getPlatformSpecificID());
        $this->assertSame(12, $format4Subtable->getOffset());
        $this->assertInstanceOf(Format4::class, $format4Subtable->getFormat());

        /** @var Format4 $format4 */
        $format4 = $format4Subtable->getFormat();
        $this->assertSame(198, $format4->getSegCountX2());
        $this->assertSame(0, $format4->getReservedPad());
        $this->assertCount(99, $format4->getEndCodes());
        $this->assertCount(99, $format4->getStartCodes());
        $this->assertCount(99, $format4->getIdDeltas());
        $this->assertCount(99, $format4->getIdRangeOffsets());

        $count = 0;
        for ($i = 0; $i < 99; ++$i) {
            $this->assertTrue($format4->getStartCodes()[$i] <= $format4->getEndCodes()[$i]);
            if ($format4->getIdRangeOffsets()[$i] !== 0) {
                $count += $format4->getEndCodes()[$i] - $format4->getStartCodes()[$i] + 1;
            }
        }

        $this->assertCount($count, $format4->getGlyphIndexArray());
    }
}
