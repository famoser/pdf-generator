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
    public function testPrintText_multipleTexts_inResultFile()
    {
        // arrange
        $text = 'hi mom';
        $printer = new Printer();
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
        $printer->getStateFactory()->getGeneralGraphicStateRepository()->setPosition($xPosition, $yPosition);

        // act
        $printer->printText('text');
        $result = $printer->save();

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
        $text = 'äüö';
        $printer = new Printer();
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
        $printer->getStateFactory()->getGeneralGraphicStateRepository()->setPosition(20, 20, 100, 100);

        // act
        $printer->printImage(ResourcesProvider::getImage1Path());
        $result = $printer->save();

        // assert
        $this->assertTrue(true);
        file_put_contents('pdf.pdf', $result);
    }
}
