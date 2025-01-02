<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Tests\Integration\Frontend;

use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;
use Famoser\PdfGenerator\Frontend\Document;
use Famoser\PdfGenerator\Frontend\Layout\AbstractElement;
use Famoser\PdfGenerator\Frontend\Layout\Parts\Row;
use Famoser\PdfGenerator\Frontend\Layout\Style\ColumnSize;
use Famoser\PdfGenerator\Frontend\Layout\Style\ElementStyle;
use Famoser\PdfGenerator\Frontend\Layout\Table;
use Famoser\PdfGenerator\Frontend\Layout\Text;
use Famoser\PdfGenerator\Frontend\Resource\Font;
use Famoser\PdfGenerator\IR\Document\Content\Common\Color;
use Famoser\PdfGenerator\Tests\Integration\Frontend\TestUtils\Render;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{
    use Render;

    public function testPrintTableOverPages(): void
    {
        // arrange
        $document = new Document([210, 297], [5, 5, 5, 5]);

        $table = new Table([ColumnSize::MINIMAL, ColumnSize::AUTO]);
        $this->setBorderStyle($table);

        $font = Font::createFromDefault();
        $normalText = new TextStyle($font);
        $header1 = new Text();
        $header1->addSpan('Description 1', $normalText, 6, 1);
        $header1->setPadding([0, 1, 10, 1]);

        $header2 = new Text();
        $header2->addSpan('Description 2', $normalText, 6, 1);
        $header2->setPadding([0, 1, 10, 1]);

        $row = new Row();
        $row->setStyle(new ElementStyle(backgroundColor: new Color(200, 100, 0)));
        $row->set(0, $header1);
        $row->set(1, $header2);
        $table->addHead($row);

        for ($i = 0; $i < 100; ++$i) {
            $header1 = new Text();
            $header1->addSpan('Content 1.'.$i, $normalText);

            $header2 = new Text();
            $header2->addSpan('Content 2.'.$i, $normalText);

            $row = new Row();
            $row->set(0, $header1);
            $row->set(1, $header2);
            $table->addBody($row);
        }

        $document->add($table);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('BT 1 0 0 1 -45.01 -5.76 cm (Content 1.3)Tj', $result);
        $this->assertStringContainsString('BT 1 -0 0 1 45.01 -0 cm (Content 2.7)Tj', $result);
    }

    private function setBorderStyle(AbstractElement $block): void
    {
        $borderedBlockStyle = new ElementStyle(1, new Color(0, 0, 0));
        $block->setStyle($borderedBlockStyle);
    }
}
