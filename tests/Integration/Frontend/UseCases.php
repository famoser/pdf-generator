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

use PdfGenerator\Frontend\Content\Paragraph;
use PdfGenerator\Frontend\Content\Style\TextStyle;
use PdfGenerator\Frontend\Document;

class UseCases
{
    public function testPrintLetter()
    {
        // arrange
        $document = new Document();

        // act
        $headerFont = Font::createFromDefault(Font::NAME_HELVETICA, Font::STYLE_ROMAN, Font::WEIGHT_BOLD);
        $headerTextStyle = new TextStyle($headerFont, 8);
        $paragraph = new Paragraph();
        $paragraph->add($headerTextStyle, 'Veröffentlichung PDF-writer');
        $document->add($paragraph);

        $bodyFont = Font::createFromDefault();
        $bodyTextStyle = new TextStyle($bodyFont, 5);
        $paragraph = new Paragraph();
        $paragraph->add($bodyTextStyle, 'Mit Freuden verkünden wir die Fertigstellung des Frontends des PDF-writers. Dies heisst, dass die API nun genug high-level ausgearbeitet wurde, damit ein produktiver Einsatz möglich wird.');
        $document->add($paragraph);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('Freuden', $result);
        $this->assertStringContainsString('Veröffentlichung', $result);
    }

    private function render(Document $document): string
    {
        $result = $document->save();
        file_put_contents('pdf.pdf', $result);

        return $result;
    }
}
