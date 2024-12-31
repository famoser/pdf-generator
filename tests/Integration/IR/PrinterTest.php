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
        $textStyle = new TextStyle($font, 12, 1, 0, Document\Content\Common\Color::createFromHex('#efefef'));

        $segment = new Text\TextSegment("Hallo Welt!\nWie geht es?", $textStyle);
        $line = new Text\TextLine(0, [$segment]);
        $text = new Text([$line], $bottomLeft);
        $page->addContent($text);

        // assert
        $result = $document->save();
        $this->assertStringContainsString('Hallo Welt!', $result);
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
        $textStyle = new TextStyle($font, 12, 1, 0, Document\Content\Common\Color::createFromHex('#000000'));

        $segment = new Text\TextSegment("Dies ist ein Test mit äöü!", $textStyle);
        $line = new Text\TextLine(0, [$segment]);
        $text = new Text([$line], $bottomLeft);
        $page->addContent($text);

        // assert
        $result = $document->save();
        $this->assertStringContainsString('Dies ist ein', $result);
        $this->assertStringContainsString('<73> <75> 13', $result);
        $this->assertStringContainsString('<c380> <c3bf> <c0>', $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintLines(): void
    {
        // arrange
        $document = new Document();
        $page = new Page('1', [210, 297]);
        $document->addPage($page);

        // act
        $bottomLeft = new Position(20, 80);
        $fontPath = ResourcesProvider::getFontOpenSansPath();
        $font = Document\Resource\Font\EmbeddedFont::create($fontPath);
        $textStyle1 = new TextStyle($font, 12, 1, 0, Document\Content\Common\Color::createFromHex('#000000'));
        $textStyle2 = new TextStyle($font, 5, 1, 1, Document\Content\Common\Color::createFromHex('#000000'));

        $segment1 = new Text\TextSegment("This is a test", $textStyle1);
        $segment2 = new Text\TextSegment("  where the phrase continues", $textStyle2);
        $segment3 = new Text\TextSegment("This is on a new line", $textStyle2);
        $line1 = new Text\TextLine(0, [$segment1, $segment2]);
        $line2 = new Text\TextLine(0, [$segment3]);
        $text = new Text([$line1, $line2], $bottomLeft);
        $page->addContent($text);

        // assert
        $result = $document->save();
        $this->assertStringContainsString('This is a test', $result);
        $this->assertStringContainsString('where the phrase', $result);
    }
}
