<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Tests\Integration\Frontend\LinearDocument;

use PdfGenerator\Frontend\Content\Paragraph;
use PdfGenerator\Frontend\Content\Style\TextStyle;
use PdfGenerator\Frontend\Layout\AbstractBlock;
use PdfGenerator\Frontend\Layout\Parts\Row;
use PdfGenerator\Frontend\Layout\Style\BlockStyle;
use PdfGenerator\Frontend\Layout\Style\ColumnSize;
use PdfGenerator\Frontend\Layout\Table;
use PdfGenerator\Frontend\LinearDocument;
use PdfGenerator\Frontend\Resource\Font;
use PdfGenerator\IR\Document\Content\Common\Color;

class TableTestCase extends LinearDocumentTestCase
{
    public function testPrintTableOverPages(): void
    {
        // arrange
        $document = new LinearDocument([210, 297], [5, 5, 5, 5]);

        $grid = new Table([ColumnSize::MINIMAL, ColumnSize::AUTO]);
        $this->setBorderStyle($grid);

        $font = Font::createFromDefault();
        $normalText = new TextStyle($font, 3, 1.2, new Color(0, 0, 0));
        $paragraph1 = new Paragraph();
        $paragraph1->add($normalText, 'Table header');

        $paragraph2 = new Paragraph();
        $paragraph2->add($normalText, 'Table header 2');

        $row = new Row();
        $row->setContent(0, $paragraph1);
        $row->setContent(1, $paragraph2);
        $grid->addHead($row);

        for ($i = 0; $i < 100; ++$i) {
            $paragraph1 = new Paragraph();
            $paragraph1->add($normalText, 'Content 1.'.$i);

            $paragraph2 = new Paragraph();
            $paragraph2->add($normalText, 'Content 2.'.$i);

            $row = new Row();
            $row->setContent(0, $paragraph1);
            $row->setContent(1, $paragraph2);
            $grid->addBody($row);
        }

        $document->add($grid);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('1 0 -0 1 11 -20 cm 0 0 10 30 re b', $result);
        $this->assertStringContainsString('1 0 -0 1 11 -32 cm 0 0 6 40 re b', $result);
    }

    private function setBorderStyle(AbstractBlock $block): void
    {
        $borderedBlockStyle = new BlockStyle();
        $borderedBlockStyle->setBorder(1, new Color(0, 0, 0));
        $block->setStyle($borderedBlockStyle);
    }
}
