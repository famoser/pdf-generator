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
use PdfGenerator\IR\Document\Font\DefaultFont;
use PdfGenerator\IR\Document\Page;
use PdfGenerator\IR\Document\Page\Content\Text\TextStyle;
use PdfGenerator\IR\Printer;
use PHPUnit\Framework\TestCase;

class PrinterTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testPrintTextTextInResultFile()
    {
        // arrange
        $document = new Document();
        $page = new Page(1, [210, 297]);
        $document->addPage($page);

        // act
        $bottomLeft = new Page\Content\Common\Position(20, 80);
        $font = $document->getOrCreateDefaultFont(DefaultFont::FONT_HELVETICA, DefaultFont::STYLE_DEFAULT);
        $textStyle = new TextStyle($font, 12, 1);

        $printer = new Printer();
        $printer->printText($page, $bottomLeft, "hallo welt\nWie geht es?", $textStyle);

        // assert
        $result = $document->save();
        $this->assertStringContainsString('hallo welt', $result);
        file_put_contents('pdf.pdf', $result);
    }
}
