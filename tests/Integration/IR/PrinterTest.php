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
        file_put_contents('pdf.pdf', $result);

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
        $printer->setCursor(new Cursor(10, $yPosition, 1));
        $printer->printRectangle($width, $height);
        $result = $printer->save();

        // assert
        $this->assertStringContainsString((string)$xPosition, $result);
        $this->assertStringContainsString((string)$yPosition, $result);
        $this->assertStringContainsString((string)$width, $result);
        $this->assertStringContainsString((string)$height, $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintText_differentDefaultFont_textAppears()
    {
        // arrange
        $document = new Document();
        $printer = new Printer($document);
        $printer->setCursor(new Cursor(20, 20, 1));

        $font = $document->getOrCreateDefaultFont(Document\Font\DefaultFont::FONT_TIMES, Document\Font\DefaultFont::STYLE_DEFAULT);
        $textStyle = new TextStyle($font, 30);
        $printer->setTextStyle($textStyle);

        // act
        $printer->printText('hi mom');
        $result = $printer->save();

        // assert
        $this->assertNotEmpty($result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintRectangle_layersAsExpected()
    {
        // arrange
        $document = new Document();
        $printer = new Printer($document);
        $rectangleStyle = new RectangleStyle(1, Color::createFromHex('#aefaef'), Color::createFromHex('#e3e3e3'));
        $printer->setRectangleStyle($rectangleStyle);

        // act
        $printer->setCursor(new Cursor(10, 10, 1));
        $printer->printRectangle(20, 20);
        $printer->setCursor(new Cursor(20, 20, 1));
        $printer->printRectangle(20, 20);
        $result = $printer->save();

        // assert
        $this->assertNotEmpty($result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintImage_imageAppears()
    {
        // arrange
        $imageSrc = ResourcesProvider::getImage1Path();
        $document = new Document();
        $printer = new Printer($document);
        $printer->setCursor(new Cursor(20, 20, 1));
        $rectangleStyle = new RectangleStyle(1, Color::createFromHex('#aefaef'), Color::createFromHex('#e3e3e3'));
        $printer->setRectangleStyle($rectangleStyle);

        // act
        $printer->printRectangle(20, 20);
        $printer->printImage($imageSrc, 20, 20);
        $result = $printer->save();

        // assert
        $this->assertNotEmpty($result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintText_withEmbeddedFont_textAppears()
    {
        // arrange
        $document = new Document();
        $printer = new Printer($document);
        $printer->setCursor(new Cursor(20, 20, 1));
        $font = $document->getOrCreateEmbeddedFont(ResourcesProvider::getFont1Path());
        $textStyle = new TextStyle($font, 12);

        // act
        $printer->setTextStyle($textStyle);
        $printer->printText('hallo');
        $result = $printer->save();
        file_put_contents('pdf.pdf', $result);

        // assert
        $this->assertNotEmpty($result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintText_withEmbeddedFont_specialCharactersAppear()
    {
        // arrange
        $document = new Document();
        $printer = new Printer($document);
        $printer->setCursor(new Cursor(50, 50, 1));
        $font = $document->getOrCreateEmbeddedFont(ResourcesProvider::getFont1Path());
        $textStyle = new TextStyle($font, 12);

        // act
        $printer->setTextStyle($textStyle);
        $printer->printText("hallo welt\nhallo welt\ninvalid ä char: ऄ");
        $result = $printer->save();

        // assert
        $this->assertNotEmpty($result);
    }
}
