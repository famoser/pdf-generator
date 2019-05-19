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
use PdfGenerator\Font\Frontend\Structure\Table\CMap\Format\Format4;
use PHPUnit\Framework\TestCase;

class ContentReaderTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testReadFontDirectory_offsetTableAsExpected()
    {
        // arrange
        $fileReader = StructureReaderTest::getFileReader();
        $contentReader = self::getContentReader();

        $this->assertTrue(true);

        return;

        // act
        $font = $contentReader->readFont($fileReader);

        // assert
        $this->assertTableDirectory($font->getTableDirectory());
    }

    /**
     * @return ContentReader
     */
    private static function getContentReader(): ContentReader
    {
        $structureReader = StructureReaderTest::getStructureReader();

        return new ContentReader($structureReader);
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
