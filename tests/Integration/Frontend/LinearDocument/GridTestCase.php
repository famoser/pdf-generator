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
use PdfGenerator\Frontend\Layout\Style\ColumnSize;
use PdfGenerator\Frontend\LinearDocument;
use PdfGenerator\IR\Document\Content\Common\Color;

class GridTestCase extends LinearDocumentTestCase
{
    public function testPrintGrid()
    {
        // arrange
        $document = new LinearDocument([210, 297], [5, 5, 5, 5]);
        $grid = new Grid(3, 10, [ColumnSize::MINIMAL, ColumnSize::MINIMAL]);

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
}
