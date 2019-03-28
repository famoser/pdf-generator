<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Tests\Integration\Frontend;

use PdfGenerator\IR\Printer;
use PdfGenerator\Tests\Resources\ResourcesProvider;
use PHPUnit\Framework\TestCase;

class PrinterTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testPrintText_textInResultFile()
    {
        // arrange
        $text = 'hi mom';
        $printer = new Printer();

        // act
        $printer->printText(20, 20, $text);
        $result = $printer->save();

        // assert
        $this->assertStringContainsString($text, $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintText_multipleTexts_inResultFile()
    {
        // arrange
        $text = 'hi mom';
        $printer = new Printer();

        // act
        $printer->printText(20, 20, $text . '1');
        $printer->printText(20, 20, $text . '2');
        $result = $printer->save();

        // assert
        $this->assertStringContainsString($text . '1', $result);
        $this->assertStringContainsString($text . '2', $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintText_cursorInResultFile()
    {
        // arrange
        $xPosition = 11;
        $yPosition = 22;
        $printer = new Printer();

        // act
        $printer->printText($xPosition, $yPosition, 'text');
        $result = $printer->save();
        file_put_contents('pdf.pdf', $result);

        // assert
        $this->assertStringContainsString((string)$xPosition, $result);
        $this->assertStringContainsString((string)$yPosition, $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintTest_withUtf8Text_inResultFile()
    {
        // arrange
        $xPosition = 11;
        $yPosition = 22;
        $printer = new Printer();

        // act
        $printer->printText($xPosition, $yPosition, 'äöü');
        $result = $printer->save();

        // assert
        $this->assertStringContainsString('äöü', $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintImage_imagePositionInResultSize()
    {
        // arrange
        $xPosition = 11;
        $yPosition = 22;
        $width = 100;
        $height = 100;
        $printer = new Printer();

        // act
        $printer->printImage($xPosition, $yPosition, $width, $height, ResourcesProvider::getImage1Path());
        $result = $printer->save();

        // assert
        $this->assertTrue(true);

        $this->assertStringContainsString((string)$xPosition, $result);
        $this->assertStringContainsString((string)$yPosition, $result);
        $this->assertStringContainsString((string)$width, $result);
        $this->assertStringContainsString((string)$height, $result);
        file_put_contents('pdf.pdf', $result);
    }
}
