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
        $printer->printText($text);
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
        $printer->setDefaultFont();
        $printer->getStateFactory()->getGeneralGraphicStateRepository()->setPosition(20, 20);

        // act
        $printer->printText($text . '1');
        $printer->printText($text . '2');
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
        $xPosition = 22;
        $yPosition = 20;
        $printer = new Printer();
        $printer->setDefaultFont();
        $printer->getStateFactory()->getGeneralGraphicStateRepository()->setPosition($xPosition, $yPosition);

        // act
        $printer->printText('text');
        $result = $printer->save();

        // assert
        $this->assertStringContainsString((string)$xPosition, $result);
        $this->assertStringContainsString((string)$yPosition, $result);
        file_put_contents('pdf.pdf', $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintLine_cursorInResultFile()
    {
        // arrange
        $xPosition = 40;
        $yPosition = 20;
        $width = 20;
        $height = 30;
        $printer = new Printer();
        $printer->setDefaultFont();
        $printer->getStateFactory()->getGeneralGraphicStateRepository()->setPosition($xPosition, $yPosition);
        $printer->getStateFactory()->getGeneralGraphicStateRepository()->setLineWidth(0.5);
        $printer->getStateFactory()->getColorStateRepository()->setFillColor('#aefaef');
        $printer->getStateFactory()->getColorStateRepository()->setBorderColor('#abccba');

        // act
        $printer->printRectangle($width, $height, true);
        $printer->printRectangle($width + 20, $height + 40, false);
        $result = $printer->save();

        // assert
        $this->assertStringContainsString((string)$xPosition, $result);
        $this->assertStringContainsString((string)$yPosition, $result);
        $this->assertStringContainsString((string)$width, $result);
        $this->assertStringContainsString((string)$height, $result);
        file_put_contents('pdf.pdf', $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintTest_withUtf8Text_inResultFile()
    {
        // arrange
        $text = 'äüö';
        $printer = new Printer();
        $printer->setDefaultFont();
        $printer->getStateFactory()->getGeneralGraphicStateRepository()->setPosition(20, 20);

        // act
        $printer->printText($text);
        $result = $printer->save();

        // assert
        $this->assertStringContainsString($text, $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintImage_inResultFile()
    {
        // arrange
        $printer = new Printer();
        $printer->setDefaultFont();
        $printer->getStateFactory()->getGeneralGraphicStateRepository()->setPosition(20, 20);
        $printer->getStateFactory()->getColorStateRepository()->setFillColor('#aefaef');
        $printer->getStateFactory()->getColorStateRepository()->setBorderColor('#abccba');

        // act
        $printer->printRectangle(20, 20, true);
        $printer->getStateFactory()->getGeneralGraphicStateRepository()->setPosition(40, 20);
        $printer->printText('hi mom');
        $result = $printer->save();
        file_put_contents('pdf.pdf', $result);

        // assert
        $this->assertTrue(true);
    }
}
