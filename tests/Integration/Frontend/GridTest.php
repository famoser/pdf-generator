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

use Famoser\PdfGenerator\Frontend\Content\Rectangle;
use Famoser\PdfGenerator\Frontend\Content\Style\DrawingStyle;
use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;
use Famoser\PdfGenerator\Frontend\Document;
use Famoser\PdfGenerator\Frontend\Layout\AbstractElement;
use Famoser\PdfGenerator\Frontend\Layout\ContentBlock;
use Famoser\PdfGenerator\Frontend\Layout\Grid;
use Famoser\PdfGenerator\Frontend\Layout\Parts\Row;
use Famoser\PdfGenerator\Frontend\Layout\Style\ColumnSize;
use Famoser\PdfGenerator\Frontend\Layout\Style\ElementStyle;
use Famoser\PdfGenerator\Frontend\Layout\Text;
use Famoser\PdfGenerator\Frontend\Resource\Font;
use Famoser\PdfGenerator\IR\Document\Content\Common\Color;
use Famoser\PdfGenerator\Tests\Integration\Frontend\TestUtils\Render;
use PHPUnit\Framework\TestCase;

class GridTest extends TestCase
{
    use Render;

    public function testPrintGridRows(): void
    {
        // arrange
        $document = new Document([210, 297], [5, 5, 5, 5]);

        $grid = new Grid(3, 10, [ColumnSize::MINIMAL, ColumnSize::MINIMAL]);
        $this->setBorderStyle($grid);

        $dimensions = [
            [[10, 5], [30, 10]],
            [[8, 8], [40, 6]],
        ];

        foreach ($dimensions as $rowDimensions) {
            $row = new Row();
            foreach ($rowDimensions as $index => $entryDimensions) {
                $rectangle = $this->createColourfulRectangle(...$entryDimensions);
                $row->set($index, new ContentBlock($rectangle));
            }

            $grid->add($row);
        }

        $document->add($grid);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('1 0 0 1 5 264 cm 1 w 0 0 0 RG 0 0 53 28 re s', $result);
        $this->assertStringContainsString('1 0 0 1 0 23 cm 0 1 1 RG 0 1 0 rg 0 0 10 5 re b', $result);
        $this->assertStringContainsString('1 0 0 1 -13 -18 cm 0 0 8 8 re b', $result);
    }

    public function testPrintFixedGrid(): void
    {
        // arrange
        $document = new Document([210, 297], [5, 5, 5, 5]);

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
        $document = new Document([210, 297], [5, 5, 5, 5]);

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
        $document = new Document([210, 297], [5, 5, 5, 5]);

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
        $document = new Document([210, 297], [5, 5, 5, 5]);

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
        $document = new Document([210, 297], [5, 5, 5, 5]);

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
        $this->assertStringContainsString('1 0 0 1 7.672 0 cm BT (Dies ist', $result);
        $this->assertStringContainsString('1 0 0 1 70.753221 0 cm BT (Aber hier', $result);
    }

    public function testPrintUnitGrid(): void
    {
        // arrange
        $document = new Document([210, 297], [5, 5, 5, 5]);

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
        $document = new Document([210, 297], [5, 5, 5, 5]);

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
        $document = new Document([210, 297], [5, 5, 5, 5]);

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

    private function setBorderStyle(AbstractElement $block): void
    {
        $borderedBlockStyle = new ElementStyle(1, new Color(0, 0, 0));
        $block->setStyle($borderedBlockStyle);
    }

    private function createColourfulRectangle(float $width, float $height): Rectangle
    {
        $colorfulRectangleStyle = new DrawingStyle(fillColor: new Color(0, 255, 0), lineColor: new Color(0, 255, 255));

        return new Rectangle($width, $height, $colorfulRectangleStyle);
    }

    private function createAlternateColourfulRow(): Row
    {
        $colorfulRectangleStyle = new ElementStyle(0.25, new Color(0, 125, 125), new Color(0, 125, 0));

        $row = new Row();
        $row->setStyle($colorfulRectangleStyle);

        return $row;
    }

    /**
     * @param float[][] $dimensions
     */
    private function printWidthRectangles(Grid $grid, array $dimensions): void
    {
        foreach ($dimensions as $widthDimensions) {
            $row = $this->createAlternateColourfulRow();
            foreach ($widthDimensions as $index => $width) {
                $rectangle = $this->createColourfulRectangle($width, 20);
                $row->set($index, new ContentBlock($rectangle));
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
        $normalText = new TextStyle($font);

        foreach ($text as $line) {
            $row = new Row();
            foreach ($line as $index => $cell) {
                $paragraph = new Text();
                $paragraph->addSpan($cell, $normalText);
                $row->set($index, $paragraph);
            }

            $grid->add($row);
        }
    }
}
