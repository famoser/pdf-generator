<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Tests\Integration\IR;

use PdfGenerator\Backend\Catalog\Font\Type0;
use PdfGenerator\Backend\Structure\Optimization\Configuration;
use PdfGenerator\IR\FlowPrinter;
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
        $composer = new FlowPrinter($document);

        // act
        $composer->printText($text);
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
        $composer = new FlowPrinter($document);

        // act
        $composer->printText($text . '1');
        $composer->printText($text . '2');
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
        $composer = new FlowPrinter($document);

        // act
        $composer->getPrinter()->setLeft(20);
        $composer->getPrinter()->setTop(20);
        $composer->getPrinter()->moveDown(10);
        $composer->printText('text');
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
        $composer = new FlowPrinter($document);

        $rectangleStyle = new RectangleStyle(0.5, Color::createFromHex('#aefaef'), Color::createFromHex('#abccba'));
        $composer->getPrinter()->getPrinter()->setRectangleStyle($rectangleStyle);

        // act
        $composer->printRectangle($width, $height);
        $composer->getPrinter()->moveRight($width);
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
        $composer = new FlowPrinter($document);

        $font = $document->getOrCreateDefaultFont(Document\Font\DefaultFont::FONT_TIMES, Document\Font\DefaultFont::STYLE_DEFAULT);
        $textStyle = new TextStyle($font, 30);
        $composer->getPrinter()->getPrinter()->setTextStyle($textStyle);

        // act
        $composer->printText('hi mom');
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
        $printer = new FlowPrinter($document);

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
        $composer = new FlowPrinter($document);
        $font = $document->getOrCreateEmbeddedFont(ResourcesProvider::getFontOpenSansPath());
        $textStyle = new TextStyle($font, 12);

        // act
        $composer->getPrinter()->getPrinter()->setTextStyle($textStyle);
        $composer->printParagraph('Hallo und Sonderzéíchèn');
        $backend = $document->render();

        $documentConfiguration = new Configuration();
        $documentConfiguration->setCreateFontSubsets(true);
        $documentConfiguration->setAutoResizeImages(true);
        $backend->setConfiguration($documentConfiguration);

        $catalog = $backend->render();
        $result = $catalog->save();
        file_put_contents('pdf.pdf', $result);

        /** @var Type0 $font */
        $font = $catalog->getPages()->getKids()[0]->getResources()->getFonts()[0];
        $type0Font = $font->getDescendantFont()->getFontDescriptor()->getFontFile3()->getFontData();
        file_put_contents('subset.ttf', $type0Font);

        // assert
        $this->assertNotEmpty($result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintTextSizing()
    {
        // arrange
        $document = new Document();
        $composer = new FlowPrinter($document);
        $font = $document->getOrCreateEmbeddedFont(ResourcesProvider::getFontOpenSansPath());
        $textStyle = new TextStyle($font, 5, 1.2);

        // act
        $composer->getPrinter()->getPrinter()->setTextStyle($textStyle);
        $composer->getPrinter()->setLeft(20);
        $composer->getPrinter()->setTop(20);
        $composer->printParagraph('Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et'); // ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.');
        $backend = $document->render();

        $documentConfiguration = new Configuration();
        $documentConfiguration->setCreateFontSubsets(true);
        $documentConfiguration->setAutoResizeImages(true);
        $backend->setConfiguration($documentConfiguration);

        $catalog = $backend->render();
        $result = $catalog->save();
        file_put_contents('pdf.pdf', $result);

        /** @var Type0 $font */
        $font = $catalog->getPages()->getKids()[0]->getResources()->getFonts()[0];
        $type0Font = $font->getDescendantFont()->getFontDescriptor()->getFontFile3()->getFontData();
        file_put_contents('subset.ttf', $type0Font);

        // assert
        $this->assertNotEmpty($result);
    }
}
