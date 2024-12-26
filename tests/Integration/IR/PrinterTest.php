<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Tests\Integration\IR;

use Famoser\PdfGenerator\IR\Document;
use Famoser\PdfGenerator\IR\Document\Content\Common\Position;
use Famoser\PdfGenerator\IR\Document\Content\Text;
use Famoser\PdfGenerator\IR\Document\Content\Text\TextStyle;
use Famoser\PdfGenerator\IR\Document\Page;
use Famoser\PdfGenerator\IR\Document\Resource\Font\DefaultFont;
use Famoser\PdfGenerator\Tests\Resources\ResourcesProvider;
use PHPUnit\Framework\TestCase;

class PrinterTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testPrintDefaultFont(): void
    {
        // arrange
        $document = new Document();
        $page = new Page('1', [210, 297]);
        $document->addPage($page);

        // act
        $bottomLeft = new Position(20, 80);
        $font = DefaultFont::create(DefaultFont::FONT_HELVETICA, DefaultFont::STYLE_DEFAULT);
        $textStyle = new TextStyle($font, 12, 1, Document\Content\Common\Color::createFromHex('#efefef'));

        $text = new Text("Hallo Welt!\nWie geht es?", $bottomLeft, $textStyle);
        $page->addContent($text);

        // assert
        $result = $document->save();
        $this->assertStringContainsString('Hallo Welt!', $result);
        file_put_contents('pdf.pdf', $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintEmbeddedFont(): void
    {
        // arrange
        $document = new Document();
        $page = new Page('1', [210, 297]);
        $document->addPage($page);

        // act
        $bottomLeft = new Position(20, 80);
        $fontPath = ResourcesProvider::getFontOpenSansPath();
        $font = Document\Resource\Font\EmbeddedFont::create($fontPath);
        $textStyle = new TextStyle($font, 12, 1, Document\Content\Common\Color::createFromHex('#000000'));

        $text = new Text("Dies ist ein Test mit äöü!\nKlappt das?", $bottomLeft, $textStyle);
        $page->addContent($text);

        // assert
        $result = $document->save();
        $this->assertStringContainsString('Dies ist ein', $result);
        $this->assertStringContainsString('<73> <75> 18', $result);
        $this->assertStringContainsString('<c380> <c3bf> <c0>', $result);
        file_put_contents('pdf.pdf', $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintPhrases(): void
    {
        // arrange
        $document = new Document();
        $page = new Page('1', [210, 297]);
        $document->addPage($page);

        // act
        $bottomLeft = new Position(20, 80);
        $fontPath = ResourcesProvider::getFontOpenSansPath();
        $font = Document\Resource\Font\EmbeddedFont::create($fontPath);
        $textStyle1 = new TextStyle($font, 12, 1, Document\Content\Common\Color::createFromHex('#000000'));
        $textStyle2 = new TextStyle($font, 5, 1, Document\Content\Common\Color::createFromHex('#000000'));

        $phrase1 = new Text\Phrase("Dies ist ein Test\nNeue Zeile. ", $textStyle1);
        $phrase2 = new Text\Phrase("Es geht weiter\nKlappt das?", $textStyle2);
        $phrase3 = new Text\Phrase('Short', $textStyle1);
        $phrase4 = new Text\Phrase('Big', $textStyle2);
        $paragraph = new Document\Content\Paragraph([$phrase1, $phrase2, $phrase3, $phrase4], $bottomLeft);
        $page->addContent($paragraph);

        // assert
        $result = $document->save();
        $this->assertStringContainsString('Dies ist ein', $result);
        $this->assertStringContainsString('Es geht wei', $result);
        file_put_contents('pdf.pdf', $result);
    }
}
