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
use PdfGenerator\Frontend\LinearPrinter;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    public function testPrintRectangle()
    {
        // arrange
        $document = new LinearPrinter();

        // act
        $rectangleStyle = new DrawingStyle();
        $rectangle = new Rectangle($rectangleStyle);
        $rectangle->setWidth(10);
        $rectangle->setHeight(20);
        $document->add($rectangle);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('10', $result);
        $this->assertStringContainsString('20', $result);
    }

    private function render(LinearPrinter $document): string
    {
        $result = $document->save();
        file_put_contents('pdf.pdf', $result);

        return $result;
    }
}
