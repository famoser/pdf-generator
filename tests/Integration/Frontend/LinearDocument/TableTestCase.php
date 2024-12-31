<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Tests\Integration\Frontend\LinearDocument;

use Famoser\PdfGenerator\Frontend\Content\TextBlock;
use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;
use Famoser\PdfGenerator\Frontend\Layout\AbstractElement;
use Famoser\PdfGenerator\Frontend\Layout\Parts\Row;
use Famoser\PdfGenerator\Frontend\Layout\Style\ElementStyle;
use Famoser\PdfGenerator\Frontend\Layout\Style\ColumnSize;
use Famoser\PdfGenerator\Frontend\Layout\Table;
use Famoser\PdfGenerator\Frontend\LinearDocument;
use Famoser\PdfGenerator\Frontend\Resource\Font;
use Famoser\PdfGenerator\IR\Document\Content\Common\Color;

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
        $paragraph1 = new TextBlock();
        $paragraph1->add($normalText, 'Table header');

        $paragraph2 = new TextBlock();
        $paragraph2->add($normalText, 'Table header 2');

        $row = new Row();
        $row->setContent(0, $paragraph1);
        $row->setContent(1, $paragraph2);
        $grid->addHead($row);

        for ($i = 0; $i < 100; ++$i) {
            $paragraph1 = new TextBlock();
            $paragraph1->add($normalText, 'Content 1.'.$i);

            $paragraph2 = new TextBlock();
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

    private function setBorderStyle(AbstractElement $block): void
    {
        $borderedBlockStyle = new ElementStyle(1, new Color(0, 0, 0));
        $block->setStyle($borderedBlockStyle);
    }
}
