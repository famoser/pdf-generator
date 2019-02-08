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
        $printer->printText($text, 20, 20);
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
        $printer->printText($text . '1', 20, 20);
        $printer->printText($text . '2', 20, 20);
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
        $printer->printText('text', $xPosition, $yPosition);
        $result = $printer->save();

        // assert
        $this->assertStringContainsString((string)$xPosition, $result);
        $this->assertStringContainsString((string)$yPosition, $result);
    }
}
