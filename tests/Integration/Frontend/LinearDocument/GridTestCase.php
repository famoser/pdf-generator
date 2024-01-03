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
use PdfGenerator\Frontend\Content\Rectangle;
use PdfGenerator\Frontend\Content\Style\DrawingStyle;
use PdfGenerator\Frontend\Content\Style\TextStyle;
use PdfGenerator\Frontend\Layout\AbstractBlock;
use PdfGenerator\Frontend\Layout\ContentBlock;
use PdfGenerator\Frontend\Layout\Grid;
use PdfGenerator\Frontend\Layout\Parts\Row;
use PdfGenerator\Frontend\Layout\Style\BlockStyle;
use PdfGenerator\Frontend\Layout\Style\ColumnSize;
use PdfGenerator\Frontend\LinearDocument;
use PdfGenerator\Frontend\Resource\Font;
use PdfGenerator\IR\Document\Content\Common\Color;

class GridTestCase extends LinearDocumentTestCase
{
    public function testPrintGridRows(): void
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
        $this->assertStringContainsString('1 0 -0 1 11 -20 cm 0 0 10 30 re b', $result);
        $this->assertStringContainsString('1 0 -0 1 11 -32 cm 0 0 6 40 re b', $result);
    }

    public function testPrintFixedGrid(): void
    {
        // arrange
        $document = new LinearDocument([210, 297], [5, 5, 5, 5]);

        $grid = new Grid(40, 20, [20, 40]);
        $this->setBorderStyle($grid);
        $dimensions = [
            [10, 30],
            [15, 20],
        ];
        $this->printWidthRectangles($grid, $dimensions);
        $document->add($grid);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('1 0 0 1 60 0 cm 0 0 30 20 re b', $result);
        $this->assertStringContainsString('1 0 0 1 60 0 cm 0 0 20 20 re b', $result);
    }

    public function testPrintMinGrid(): void
    {
        // arrange
        $document = new LinearDocument([210, 297], [5, 5, 5, 5]);

        $grid = new Grid(40, 20, [ColumnSize::MINIMAL, ColumnSize::MINIMAL]);
        $this->setBorderStyle($grid);
        $dimensions = [
            [10, 30],
            [15, 20],
        ];
        $this->printWidthRectangles($grid, $dimensions);
        $document->add($grid);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('1 0 0 1 55 0 cm 0 0 30 20 re', $result);
        $this->assertStringContainsString('1 0 0 1 55 0 cm 0 0 20 20 re', $result);
    }

    public function testPrintAutoGrid(): void
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
        $this->assertStringContainsString('1 0 0 1 100 0 cm 0 0 20 20 re b', $result);
        $this->assertStringContainsString('1 0 0 1 100 0 cm 0 0 40 20 re b', $result);
    }

    public function testAutoSizingGrid(): void
    {
        // arrange
        $document = new LinearDocument([210, 297], [5, 5, 5, 5]);

        $grid = new Grid(10, 0, [ColumnSize::AUTO, ColumnSize::AUTO]);
        $this->setBorderStyle($grid);
        $this->printWidthRectangles($grid, [[10, 20], [20, 10]]);
        $document->add($grid);

        $grid = new Grid(10, 0, [ColumnSize::AUTO, ColumnSize::AUTO]);
        $this->setBorderStyle($grid);
        $this->printWidthRectangles($grid, [[10, 20], [10, 20]]);
        $document->add($grid);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('1 0 0 1 -105 -40 cm 0 0 0 RG 0 0 0 rg 0 0 200 40 re', $result);
        $this->assertStringContainsString('1 0 0 1 73.333333 0 cm 0 0 20 20 re', $result);
    }

    public function testAutoSizingTextGrid(): void
    {
        // arrange
        $document = new LinearDocument([210, 297], [5, 5, 5, 5]);

        $grid = new Grid(1, 2, [ColumnSize::MINIMAL, ColumnSize::AUTO, ColumnSize::AUTO]);
        $this->setBorderStyle($grid);
        $text = [
            ['23', 'Bitte die grössten nicht.', 'hi'],
            ['231', 'Dies ist immer noch am längesten.', 'Aber hier viel mehr Gewicht; daher sollte dieser Teil insgesamt doch mehr Platz eingeräumt werden'],
        ];
        $this->printText($grid, $text);
        $document->add($grid);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('1 0 0 1 6.004 0 cm BT', $result);
        $this->assertStringContainsString('1 0 0 1 71.361331 0 cm BT', $result);
    }

    public function testPrintUnitGrid(): void
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
        $this->assertStringContainsString('1 0 0 1 152.5 0 cm 0 0 50 20 re b', $result);
        $this->assertStringContainsString('1 0 0 1 152.5 0 cm 0 0 40 20 re b', $result);
    }

    public function testPrintDiverseGrid(): void
    {
        // arrange
        $document = new LinearDocument([210, 297], [5, 5, 5, 5]);

        $grid = new Grid(2, 5, [ColumnSize::AUTO, 5, ColumnSize::MINIMAL, '3'.ColumnSize::UNIT, ColumnSize::UNIT]);
        $this->setBorderStyle($grid);
        $dimensions = [
            [10, 5, 4, 3, 10],
            [8, 5, 5, 20, 10],
        ];
        $this->printWidthRectangles($grid, $dimensions);
        $document->add($grid);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('1 0 0 1 7 0 cm 0 0 3 20 re b', $result);
        $this->assertStringContainsString('1 0 0 1 7 0 cm 0 0 20 20 re b', $result);
    }

    public function testEmptyGrid(): void
    {
        // arrange
        $document = new LinearDocument([210, 297], [5, 5, 5, 5]);

        $grid = new Grid(3, 10, [ColumnSize::AUTO, ColumnSize::MINIMAL, 10, '2'.ColumnSize::UNIT]);
        $this->setBorderStyle($grid);
        $document->add($grid);

        $grid = new Grid();
        $this->setBorderStyle($grid);
        $document->add($grid);

        // assert
        $result = $this->render($document);
        $this->assertNotEmpty($result);
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

    private function createAlternateColourfulRow(): Row
    {
        $colorfulRectangleStyle = new BlockStyle();
        $colorfulRectangleStyle->setBackgroundColor(new Color(0, 125, 0));
        $colorfulRectangleStyle->setBorder(0.25, new Color(0, 125, 125));

        $row = new Row();
        $row->setStyle($colorfulRectangleStyle);

        return $row;
    }

    /**
     * @param float[][] $dimensions
     */
    private function printWidthRectangles(Grid $grid, array $dimensions): void
    {
        $rectangle = $this->createColourfulRectangle();

        foreach ($dimensions as $widthDimensions) {
            $row = $this->createAlternateColourfulRow();
            foreach ($widthDimensions as $index => $width) {
                $contentBlock = new ContentBlock($rectangle);
                $contentBlock->setHeight(20);
                $contentBlock->setWidth($width);

                $row->set($index, $contentBlock);
            }

            $grid->add($row);
        }
    }

    /**
     * @param string[][] $text
     */
    private function printText(Grid $grid, array $text): void
    {
        $font = Font::createFromDefault();
        $normalText = new TextStyle($font, 3);

        foreach ($text as $line) {
            $row = new Row();
            foreach ($line as $index => $cell) {
                $paragraph = new Paragraph();
                $paragraph->add($normalText, $cell);
                $row->setContent($index, $paragraph);
            }

            $grid->add($row);
        }
    }
}
