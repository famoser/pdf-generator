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

use PdfGenerator\Backend\Structure\Optimization\Configuration;
use PdfGenerator\IR\Composer;
use PdfGenerator\IR\Structure\Document;
use PdfGenerator\IR\Structure\Document\Page\Content\Common\Color;
use PdfGenerator\IR\Structure\Document\Page\Content\Rectangle\RectangleStyle;
use PdfGenerator\IR\Structure\Document\Page\Content\Text\TextStyle;
use PdfGenerator\Tests\Resources\ResourcesProvider;
use PHPUnit\Framework\TestCase;

class ComposerTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testPrintTextTextInResultFile()
    {
        // arrange
        $text = 'hi mom';
        $document = new Document();
        $composer = new Composer($document);

        // act
        $composer->printPhrase($text);
        $result = $document->render()->save();
        file_put_contents('pdf.pdf', $result);

        // assert
        $this->assertStringContainsString($text, $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintTextMultipleTextsInResultFile()
    {
        // arrange
        $text = 'hi mom';
        $document = new Document();
        $composer = new Composer($document);

        // act
        $composer->printPhrase($text . '1');
        $composer->printPhrase($text . '2');
        $result = $document->render()->save();
        file_put_contents('pdf.pdf', $result);

        // assert
        $this->assertStringContainsString($text . '1', $result);
        $this->assertStringContainsString($text . '2', $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintTextCursorInResultFile()
    {
        // arrange
        $document = new Document();
        $composer = new Composer($document);

        // act
        $composer->setPageMargin(20);
        $composer->resetCursor();
        $composer->moveCursor(10);
        $composer->printPhrase('text');
        $result = $document->render()->save();

        // assert
        $this->assertStringContainsString((string)(297 - 30), $result);
        $this->assertStringContainsString((string)(20), $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintLineCursorInResultFile()
    {
        // arrange
        $width = 20;
        $height = 30;
        $document = new Document();
        $composer = new Composer($document);

        $rectangleStyle = new RectangleStyle(0.5, Color::createFromHex('#aefaef'), Color::createFromHex('#abccba'));
        $composer->getPrinter()->setRectangleStyle($rectangleStyle);

        // act
        $composer->printRectangle($width, $height);
        $composer->moveCursor(0, $width);
        $composer->printRectangle($width + $width, $height + $height);
        $result = $document->render()->save();

        // assert
        $this->assertStringContainsString((string)($width + $width), $result);
        $this->assertStringContainsString((string)($height + $height), $result);
        $this->assertStringContainsString((string)$width, $result);
        $this->assertStringContainsString((string)$height, $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintTextDifferentDefaultFontTextAppears()
    {
        // arrange
        $document = new Document();
        $composer = new Composer($document);

        $font = $document->getOrCreateDefaultFont(Document\Font\DefaultFont::FONT_TIMES, Document\Font\DefaultFont::STYLE_DEFAULT);
        $textStyle = new TextStyle($font, 30);
        $composer->getPrinter()->setTextStyle($textStyle);

        // act
        $composer->printPhrase('hi mom');
        $result = $document->render()->save();
        file_put_contents('pdf.pdf', $result);

        // assert
        $this->assertNotEmpty($result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintImageImageAppears()
    {
        // arrange
        $imageSrc = ResourcesProvider::getImage1Path();
        $document = new Document();
        $printer = new Composer($document);

        // act
        $image = $document->getOrCreateImage($imageSrc);
        $printer->printImage($image, 20, 20);
        $result = $document->render()->save();
        file_put_contents('pdf.pdf', $result);

        // assert
        $this->assertNotEmpty($result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintTextWithEmbeddedFontTextAppears()
    {
        // arrange
        $document = new Document();
        $composer = new Composer($document);
        $font = $document->getOrCreateEmbeddedFont(ResourcesProvider::getFontOpenSansPath());
        $textStyle = new TextStyle($font, 12);
        $imageSrc = ResourcesProvider::getImage1Path();

        // act
        $composer->getPrinter()->setTextStyle($textStyle);
        $composer->printPhrase('Hallo Welt und Sonderzéíchèn');
        $image = $document->getOrCreateImage($imageSrc);
        $composer->printImage($image, 20, 20);
        $backend = $document->render();

        $documentConfiguration = new Configuration();
        $documentConfiguration->setCreateFontSubsets(true);
        $documentConfiguration->setAutoResizeImages(true);
        $backend->setConfiguration($documentConfiguration);

        $result = $backend->save();
        file_put_contents('pdf.pdf', $result);

        // assert
        $this->assertNotEmpty($result);
    }
}
