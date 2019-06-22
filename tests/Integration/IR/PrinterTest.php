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

use PdfGenerator\IR\Cursor;
use PdfGenerator\IR\Printer;
use PdfGenerator\IR\Structure\Document;
use PdfGenerator\IR\Structure\Document\Page\Content\Common\Color;
use PdfGenerator\IR\Structure\Document\Page\Content\Rectangle\RectangleStyle;
use PdfGenerator\IR\Structure\Document\Page\Content\Text\TextStyle;
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
        $printer = new Printer(new Document());

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
        $printer = new Printer(new Document());
        $printer->setCursor(new Cursor(20, 20, 1));

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
        $printer = new Printer(new Document());
        $printer->setCursor(new Cursor($xPosition, $yPosition, 1));
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
        $printer = new Printer(new Document());
        $printer->setCursor(new Cursor($xPosition, $yPosition, 1));

        $rectangleStyle = new RectangleStyle(0.5, Color::createFromHex('#aefaef'), Color::createFromHex('#abccba'));
        $printer->setRectangleStyle($rectangleStyle);

        // act
        $printer->printRectangle($width, $height);
        $printer->printRectangle($width + 20, $height + 40);
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
        $text = 'a ää z';
        $printer = new Printer(new Document());
        $printer->setCursor(new Cursor(20, 20, 1));

        // act
        $printer->printText($text);
        $result = $printer->save();
        file_put_contents('pdf.pdf', $result);

        // assert
        $this->assertStringContainsString($text, $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintImage_inResultFile()
    {
        // arrange
        $document = new Document();
        $printer = new Printer($document);
        $printer->setCursor(new Cursor(20, 20, 1));

        $font = $document->getOrCreateDefaultFont(Document\Font\DefaultFont::FONT_COURIER, Document\Font\DefaultFont::STYLE_DEFAULT);
        $textStyle = new TextStyle($font, 30);
        $printer->setTextStyle($textStyle);

        // act
        $printer->printRectangle(20, 20);
        $printer->printText('hi mom');
        $result = $printer->save();

        // assert
        $this->assertTrue(true);
    }
}
