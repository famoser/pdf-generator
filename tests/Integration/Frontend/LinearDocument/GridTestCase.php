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

use PdfGenerator\Frontend\Content\Rectangle;
use PdfGenerator\Frontend\Content\Style\DrawingStyle;
use PdfGenerator\Frontend\Layout\ContentBlock;
use PdfGenerator\Frontend\Layout\Grid;
use PdfGenerator\Frontend\Layout\Parts\Row;
use PdfGenerator\Frontend\Layout\Style\BlockStyle;
use PdfGenerator\Frontend\Layout\Style\ColumnSize;
use PdfGenerator\Frontend\LinearDocument;
use PdfGenerator\IR\Document\Content\Common\Color;

class GridTestCase extends LinearDocumentTestCase
{
    public function testPrintMinGrid()
    {
        // arrange
        $document = new LinearDocument([210, 297], [5, 5, 5, 5]);
        $grid = new Grid(3, 10, [ColumnSize::MINIMAL, ColumnSize::MINIMAL]);

        $borderedBlockStyle = new BlockStyle();
        $borderedBlockStyle->setBorder(1, new Color(0, 0, 0));
        $grid->setStyle($borderedBlockStyle);

        $rectangleStyle = new DrawingStyle();
        $rectangleStyle->setFillColor(new Color(0, 255, 0));
        $rectangleStyle->setLineColor(new Color(0, 255, 255));
        $rectangle = new Rectangle($rectangleStyle);

        $dimensions = [
            [[10, 5], [30, 10]],
            [[8, 8], [40, 6]],
        ];

        foreach ($dimensions as $rowDimensions) {
            $row = new Row();
            foreach ($rowDimensions as $index => $entryDimensions) {
                $contentBlock = new ContentBlock($rectangle);
                $contentBlock->setHeight($entryDimensions[0]);
                $contentBlock->setWidth($entryDimensions[1]);

                $row->set($index, $contentBlock);
            }

            $grid->add($row);
        }

        $document->add($grid);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('10', $result);
        $this->assertStringContainsString('20', $result);
        $this->assertStringContainsString('40', $result);
    }

    public function testPrintAutoGrid()
    {
        // arrange
        $document = new LinearDocument([210, 297], [5, 5, 5, 5]);
        $grid = new Grid(40, 20, [20, ColumnSize::AUTO, 40]);

        $borderedBlockStyle = new BlockStyle();
        $borderedBlockStyle->setBorder(1, new Color(0, 0, 0));
        $grid->setStyle($borderedBlockStyle);

        $colorfulRectangleStyle = new DrawingStyle();
        $colorfulRectangleStyle->setFillColor(new Color(0, 255, 0));
        $colorfulRectangleStyle->setLineColor(new Color(0, 255, 255));
        $rectangle = new Rectangle($colorfulRectangleStyle);

        $dimensions = [
            [10, 50, 20],
            [5, 70, 40],
        ];

        foreach ($dimensions as $widthDimensions) {
            $row = new Row();
            foreach ($widthDimensions as $index => $width) {
                $contentBlock = new ContentBlock($rectangle);
                $contentBlock->setHeight(20);
                $contentBlock->setWidth($width);

                $row->set($index, $contentBlock);
            }

            $grid->add($row);
        }

        $document->add($grid);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('10', $result);
        $this->assertStringContainsString('20', $result);
        $this->assertStringContainsString('40', $result);
    }
}
