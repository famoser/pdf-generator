<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Tests\Integration\Font\Frontend;

use Famoser\PdfGenerator\Font\Frontend\File\Table\CMap\Format\Format4;
use Famoser\PdfGenerator\Font\Frontend\File\Table\CMap\FormatReader;
use Famoser\PdfGenerator\Font\Frontend\File\Table\CMapTable;
use Famoser\PdfGenerator\Font\Frontend\File\Table\GlyfTable;
use Famoser\PdfGenerator\Font\Frontend\File\Table\HeadTable;
use Famoser\PdfGenerator\Font\Frontend\File\Table\HHeaTable;
use Famoser\PdfGenerator\Font\Frontend\File\Table\HMtxTable;
use Famoser\PdfGenerator\Font\Frontend\File\Table\LocaTable;
use Famoser\PdfGenerator\Font\Frontend\File\Table\MaxPTable;
use Famoser\PdfGenerator\Font\Frontend\File\Table\NameTable;
use Famoser\PdfGenerator\Font\Frontend\FileReader;
use Famoser\PdfGenerator\Font\Frontend\StreamReader;
use PHPUnit\Framework\TestCase;

class FileReaderTest extends TestCase
{
    private static string $defaultFilePath = __DIR__.\DIRECTORY_SEPARATOR.'OpenSans-Regular.ttf';

    public static function getDefaultFontContent(): string
    {
        return file_get_contents(self::$defaultFilePath);
    }

    public static function getFileReader(): StreamReader
    {
        $content = self::getDefaultFontContent();

        return new StreamReader($content);
    }

    public static function getStructureReader(): FileReader
    {
        $cMapFormatReader = new FormatReader();
        $postFormatReader = new \Famoser\PdfGenerator\Font\Frontend\File\Table\Post\FormatReader();

        return new FileReader($cMapFormatReader, $postFormatReader);
    }

    /**
     * @throws \Exception
     */
    public function testReadFontFontAsExpected(): void
    {
        // arrange
        $fileReader = self::getFileReader();
        $structureReader = self::getStructureReader();

        $font = $structureReader->read($fileReader);

        // assert
        $this->assertCMapTable($font->getCMapTable());
        $this->assertLocaTable($font->getLocaTable());
        $this->assertHeadTable($font->getHeadTable());
        $this->assertMaxPTable($font->getMaxPTable());
        $this->assertGlyfTable($font->getGlyfTables());
        $this->assertHHeaTable($font->getHHeaTable());
        $this->assertHMtxTable($font->getHMtxTable());
        $this->assertNameTable($font->getNameTable());
        $this->assertCount(0, $font->getRawTables());
    }

    private function assertCMapTable(CMapTable $cmapTable): void
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
            if (0 !== $format4->getIdRangeOffsets()[$i]) {
                $count += $format4->getEndCodes()[$i] - $format4->getStartCodes()[$i] + 1;
            }
        }

        $this->assertCount($count, $format4->getGlyphIndexArray());
    }

    private function assertLocaTable(LocaTable $locaTable): void
    {
        $this->assertCount(939, $locaTable->getOffsets());

        $size = \count($locaTable->getOffsets()) - 1;
        for ($i = 0; $i < $size; ++$i) {
            $this->assertTrue($locaTable->getOffsets()[$i] <= $locaTable->getOffsets()[$i + 1]);
        }
    }

    private function assertHeadTable(HeadTable $headTable): void
    {
        $this->assertSame(1, $headTable->getMajorVersion());
        $this->assertSame(0x5F0F3CF5, $headTable->getMagicNumber());
        $this->assertSame(2048, $headTable->getUnitsPerEm());
        $this->assertSame(0, $headTable->getMacStyle());
        $this->assertSame(2, $headTable->getFontDirectionHints());
    }

    private function assertMaxPTable(MaxPTable $maxPTable): void
    {
        $this->assertSame(938, $maxPTable->getNumGlyphs());
        $this->assertSame(2, $maxPTable->getMaxZones());
        $this->assertSame(1, $maxPTable->getMaxComponentDepth());
    }

    /**
     * @param (GlyfTable|null)[] $glyfTables
     */
    private function assertGlyfTable(array $glyfTables): void
    {
        $this->assertCount(938, $glyfTables);

        foreach ($glyfTables as $glyfTable) {
            if (null === $glyfTable) {
                continue;
            }

            $this->assertTrue($glyfTable->getXMin() <= $glyfTable->getXMax());
            $this->assertTrue($glyfTable->getYMin() <= $glyfTable->getYMax());
        }
    }

    private function assertHHeaTable(HHeaTable $hHeaTable): void
    {
        $this->assertSame(-600, $hHeaTable->getDescent());
        $this->assertSame(1, $hHeaTable->getCaretSlopeRise());
        $this->assertSame(931, $hHeaTable->getNumOfLongHorMetrics());
    }

    private function assertHMtxTable(HMtxTable $hMtxTable): void
    {
        $this->assertCount(931, $hMtxTable->getLongHorMetrics());

        $someEntry = $hMtxTable->getLongHorMetrics()[930];
        $this->assertSame(571, $someEntry->getAdvanceWidth());
        $this->assertSame(201, $someEntry->getLeftSideBearing());

        $this->assertSame(201, $hMtxTable->getLeftSideBearings()[1]);
    }

    private function assertNameTable(NameTable $nameTable): void
    {
        $this->assertCount(8, $nameTable->getNameRecords());

        // the encoding looks messed up, but the calculated offsets are correct (hence the "value" read is correct
        // either the font file is broken, or the used encoding is indeed very weird
        $firstCharFirstValue = substr($nameTable->getNameRecords()[0]->getValue(), 1, 1);
        $firstCharSecondValue = substr($nameTable->getNameRecords()[1]->getValue(), 1, 1);
        $this->assertEquals('D', $firstCharFirstValue);
        $this->assertEquals('O', $firstCharSecondValue);
    }
}
