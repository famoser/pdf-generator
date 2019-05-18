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
use PdfGenerator\Font\Frontend\StructureReader;
use PHPUnit\Framework\TestCase;

class StructureReaderTest extends TestCase
{
    private static $defaultFile = __DIR__ . 'OpenSans-Regular.ttf';

    /**
     * @return FileReader
     */
    private static function getFileReader()
    {
        $content = self::$defaultFile;

        return new FileReader($content);
    }

    /**
     * @throws \Exception
     */
    public function ignore_testReadFontDirectory_offsetTableAsExpected()
    {
        // arrange
        $fileReader = $this->getFileReader();
        $structureReader = new StructureReader();

        $fontDirectory = $structureReader->readFontDirectory($fileReader);

        // assert
        var_dump($fontDirectory->getOffsetTable());
    }

    /**
     * @throws \Exception
     */
    public function testOk()
    {
        $this->assertTrue(true);
    }
}
