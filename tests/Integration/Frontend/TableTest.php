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
use Famoser\PdfGenerator\Frontend\Content\TextBlock;
use Famoser\PdfGenerator\Frontend\Layout\AbstractElement;
use Famoser\PdfGenerator\Frontend\Layout\Parts\Row;
use Famoser\PdfGenerator\Frontend\Layout\Style\ColumnSize;
use Famoser\PdfGenerator\Frontend\Layout\Style\ElementStyle;
use Famoser\PdfGenerator\Frontend\Layout\Table;
use Famoser\PdfGenerator\Frontend\Document;
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

        $grid = new Table([ColumnSize::MINIMAL, ColumnSize::AUTO]);
        $this->setBorderStyle($grid);

        $font = Font::createFromDefault();
        $normalText = new TextStyle($font);
        $paragraph1 = new Text();
        $paragraph1->addSpan('Table header', $normalText);

        $paragraph2 = new Text();
        $paragraph2->addSpan('Table header 2', $normalText);

        $row = new Row();
        $row->set(0, $paragraph1);
        $row->set(1, $paragraph2);
        $grid->addHead($row);

        for ($i = 0; $i < 100; ++$i) {
            $paragraph1 = new Text();
            $paragraph1->addSpan('Content 1.'.$i, $normalText);

            $paragraph2 = new Text();
            $paragraph2->addSpan('Content 2.'.$i, $normalText);

            $row = new Row();
            $row->set(0, $paragraph1);
            $row->set(1, $paragraph2);
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
