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
use PdfGenerator\Font\Frontend\Structure\Table\CMap\Format\Format4;
use PdfGenerator\Font\Frontend\Structure\Table\CMap\FormatReader;
use PdfGenerator\Font\Frontend\Structure\Table\CMapTable;
use PdfGenerator\Font\Frontend\Structure\Table\GlyfTable;
use PdfGenerator\Font\Frontend\Structure\Table\HeadTable;
use PdfGenerator\Font\Frontend\Structure\Table\HHeaTable;
use PdfGenerator\Font\Frontend\Structure\Table\HMtxTable;
use PdfGenerator\Font\Frontend\Structure\Table\LocaTable;
use PdfGenerator\Font\Frontend\Structure\Table\MaxPTable;
use PdfGenerator\Font\Frontend\Structure\Table\OffsetTable;
use PdfGenerator\Font\Frontend\Structure\Table\TableDirectoryEntry;
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
    public function testReadFont_fontAsExpected()
    {
        // arrange
        $fileReader = self::getFileReader();
        $structureReader = self::getStructureReader();

        $font = $structureReader->readFont($fileReader);

        // assert
        $this->assertOffsetTable($font->getOffsetTable());
        $this->assertTableDirectoryEntries($font->getTableDirectoryEntries());
        $this->assertCMapTable($font->getCMapTable());
        $this->assertLocaTable($font->getLocaTable());
        $this->assertHeadTable($font->getHeadTable());
        $this->assertMaxPTable($font->getMaxPTable());
        $this->assertGlyfTable($font->getGlyfTables());
        $this->assertHHeaTable($font->getHHeaTable());
        $this->assertHMtxTable($font->getHMtxTable());
        $this->assertCount(0, $font->getRawTables());
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

    private function assertLocaTable(LocaTable $locaTable)
    {
        $this->assertCount(939, $locaTable->getOffsets());

        $size = \count($locaTable->getOffsets()) - 1;
        for ($i = 0; $i < $size; ++$i) {
            $this->assertTrue($locaTable->getOffsets()[$i] <= $locaTable->getOffsets()[$i + 1]);
        }
    }

    private function assertHeadTable(?HeadTable $headTable)
    {
        $this->assertSame(1, $headTable->getMajorVersion());
        $this->assertSame(0x5F0F3CF5, $headTable->getMagicNumber());
        $this->assertSame(2048, $headTable->getUnitsPerEm());
        $this->assertSame(0, $headTable->getMacStyle());
        $this->assertSame(2, $headTable->getFontDirectionHints());
    }

    private function assertMaxPTable(?MaxPTable $maxPTable)
    {
        $this->assertSame(938, $maxPTable->getNumGlyphs());
        $this->assertSame(2, $maxPTable->getMaxZones());
        $this->assertSame(1, $maxPTable->getMaxComponentDepth());
    }

    /**
     * @param GlyfTable[] $glyfTables
     */
    private function assertGlyfTable(array $glyfTables)
    {
        $this->assertCount(938, $glyfTables);

        foreach ($glyfTables as $glyfTable) {
            if ($glyfTable === null) {
                continue;
            }

            $this->assertTrue($glyfTable->getXMin() <= $glyfTable->getXMax());
            $this->assertTrue($glyfTable->getYMin() <= $glyfTable->getYMax());
        }
    }

    /**
     * @param HHeaTable $hHeaTable
     */
    private function assertHHeaTable(HHeaTable $hHeaTable)
    {
        $this->assertSame(-600.0, $hHeaTable->getDecent());
        $this->assertSame(1, $hHeaTable->getCaretSlopeRise());
        $this->assertSame(931, $hHeaTable->getNumOfLongHorMetrics());
    }

    /**
     * @param HMtxTable $hMtxTable
     */
    private function assertHMtxTable(HMtxTable $hMtxTable)
    {
        $this->assertCount(931, $hMtxTable->getLongHorMetrics());

        $someEntry = $hMtxTable->getLongHorMetrics()[930];
        $this->assertSame(571, $someEntry->getAdvanceWidth());
        $this->assertSame(201, $someEntry->getLeftSideBearing());

        $this->assertSame(201, $hMtxTable->getLeftSideBearings()[1]);
    }
}
