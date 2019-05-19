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

use PdfGenerator\Font\Frontend\Content\CMapFormatDirectory;
use PdfGenerator\Font\Frontend\Content\TableDirectory;
use PdfGenerator\Font\Frontend\ContentReader;
use PdfGenerator\Font\Frontend\Structure\CMapFormatReader;
use PdfGenerator\Font\Frontend\Structure\Table\CMapFormat\Format4;
use PHPUnit\Framework\TestCase;

class ContentReaderTest extends TestCase
{
    public function testAssert()
    {
        $this->assertTrue(true);
    }

    /**
     * @throws \Exception
     */
    public function skipped_testReadFontDirectory_offsetTableAsExpected()
    {
        // arrange
        $fileReader = StructureReaderTest::getFileReader();
        $contentReader = self::getContentReader();

        // act
        $font = $contentReader->readFont($fileReader);
        var_dump($font);

        // assert
        $this->assertTableDirectory($font->getTableDirectory());
    }

    /**
     * @return ContentReader
     */
    private static function getContentReader(): ContentReader
    {
        $structureReader = StructureReaderTest::getStructureReader();
        $cmapFormatReader = new CMapFormatReader();

        return new ContentReader($cmapFormatReader, $structureReader);
    }

    /**
     * @param TableDirectory $tableDirectory
     */
    private function assertTableDirectory(TableDirectory $tableDirectory)
    {
        $cmapFormatDirectory = $tableDirectory->getCmapFormatDirectory();
        $this->assertCMapFormatDirectory($cmapFormatDirectory);
    }

    /**
     * @param CMapFormatDirectory $cMapFormatDirectory
     */
    private function assertCMapFormatDirectory(CMapFormatDirectory $cMapFormatDirectory)
    {
        $this->assertFormat4($cMapFormatDirectory->getFormat4());
    }

    /**
     * @param Format4 $format4
     */
    private function assertFormat4(Format4 $format4)
    {
        $this->assertSame(4, $format4->getFormat());
    }
}
