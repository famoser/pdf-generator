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

use PdfGenerator\Frontend\Layout\Content\Rectangle;
use PdfGenerator\Frontend\Layout\Content\Style\DrawingStyle;
use PdfGenerator\Frontend\Layout\Flow;
use PdfGenerator\Frontend\LinearDocument;
use PHPUnit\Framework\TestCase;

class LinearDocumentTest extends TestCase
{
    public function testPrintRectangle()
    {
        // arrange
        $document = new LinearDocument([210, 297], [5, 5, 5, 5]);

        // act
        $rectangleStyle = new DrawingStyle();
        $rectangle = new Rectangle($rectangleStyle);
        $rectangle->setMargin([20, 0, 0, 0]);
        $rectangle->setPadding([0, 0, 0, 40]);
        $rectangle->setWidth(10);
        $rectangle->setHeight(20);
        $document->add($rectangle);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('10', $result);
        $this->assertStringContainsString('20', $result);
        $this->assertStringContainsString('25', $result);
        $this->assertStringContainsString('45', $result);
    }

    public function testPrintFlowRectangles()
    {
        // arrange
        $document = new LinearDocument();

        // act
        $rectangleStyle = new DrawingStyle();
        $flow = new Flow();
        for ($i = 0; $i < 10; ++$i) {
            $rectangle = new Rectangle($rectangleStyle);
            $rectangle->setWidth($i * 5 % 40);
            $rectangle->setHeight($i * 3 % 17);
            $flow->add($rectangle);
        }
        $document->add($flow);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('10', $result);
        $this->assertStringContainsString('20', $result);
    }

    private function render(LinearDocument $document): string
    {
        $result = $document->save();
        file_put_contents('pdf.pdf', $result);

        return $result;
    }
}
