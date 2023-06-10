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

use PdfGenerator\IR\Document;
use PdfGenerator\IR\Document\Content\Text\TextStyle;
use PdfGenerator\IR\Document\Page;
use PdfGenerator\IR\Document\Resource\Font\DefaultFont;
use PdfGenerator\IR\Printer;
use PdfGenerator\Tests\Resources\ResourcesProvider;
use PHPUnit\Framework\TestCase;

class PrinterTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testPrintDefaultFont()
    {
        // arrange
        $document = new Document();
        $page = new Page(1, [210, 297]);
        $document->addPage($page);

        // act
        $bottomLeft = new Document\Content\Common\Position(20, 80);
        $font = $document->getOrCreateDefaultFont(DefaultFont::FONT_HELVETICA, DefaultFont::STYLE_DEFAULT);
        $textStyle = new TextStyle($font, 12, 1);

        $printer = new Printer();
        $printer->printText($page, $bottomLeft, "Hallo Welt!\nWie geht es?", $textStyle);

        // assert
        $result = $document->save();
        $this->assertStringContainsString('Hallo Welt!', $result);
        file_put_contents('pdf.pdf', $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintEmbeddedFont()
    {
        // arrange
        $document = new Document();
        $page = new Page(1, [210, 297]);
        $document->addPage($page);

        // act
        $bottomLeft = new Document\Content\Common\Position(20, 80);
        $fontPath = ResourcesProvider::getFontOpenSansPath();
        $font = $document->getOrCreateEmbeddedFont($fontPath);
        $textStyle = new TextStyle($font, 12, 1);

        $printer = new Printer();
        $printer->printText($page, $bottomLeft, "Dies ist ein Test mit äöü!\nKlappt das?", $textStyle);

        // assert
        $result = $document->save();
        $this->assertStringContainsString('Dies ist ein', $result);
        file_put_contents('pdf.pdf', $result);
    }
}
