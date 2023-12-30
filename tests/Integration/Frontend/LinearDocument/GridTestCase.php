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
use PdfGenerator\Frontend\Layout\AbstractBlock;
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
        $this->setBorderStyle($grid);

        $dimensions = [
            [[10, 5], [30, 10]],
            [[8, 8], [40, 6]],
        ];

        $rectangle = $this->createColourfulRectangle();
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
        $this->setBorderStyle($grid);
        $dimensions = [
            [10, 50, 20],
            [5, 70, 40],
        ];
        $this->printWidthRectangles($grid, $dimensions);
        $document->add($grid);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('20', $result);
        $this->assertStringContainsString('232', $result);
        $this->assertStringContainsString('200', $result);
    }

    public function testPrintUnitGrid()
    {
        // arrange
        $document = new LinearDocument([210, 297], [5, 5, 5, 5]);

        $grid = new Grid(10, 20, ['3'.ColumnSize::UNIT, ColumnSize::UNIT]);
        $this->setBorderStyle($grid);
        $dimensions = [
            [10, 50],
            [5, 40],
        ];
        $this->printWidthRectangles($grid, $dimensions);
        $document->add($grid);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('10', $result);
        $this->assertStringContainsString('20', $result);
        $this->assertStringContainsString('40', $result);
    }

    private function setBorderStyle(AbstractBlock $block): void
    {
        $borderedBlockStyle = new BlockStyle();
        $borderedBlockStyle->setBorder(1, new Color(0, 0, 0));
        $block->setStyle($borderedBlockStyle);
    }

    private function createColourfulRectangle(): Rectangle
    {
        $colorfulRectangleStyle = new DrawingStyle();
        $colorfulRectangleStyle->setFillColor(new Color(0, 255, 0));
        $colorfulRectangleStyle->setLineColor(new Color(0, 255, 255));

        return new Rectangle($colorfulRectangleStyle);
    }

    /**
     * @param float[][] $dimensions
     */
    private function printWidthRectangles(Grid $grid, array $dimensions): void
    {
        $rectangle = $this->createColourfulRectangle();

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
    }
}
