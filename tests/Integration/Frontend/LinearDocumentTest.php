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

use PdfGenerator\Frontend\Content\Paragraph;
use PdfGenerator\Frontend\Content\Rectangle;
use PdfGenerator\Frontend\Content\Style\DrawingStyle;
use PdfGenerator\Frontend\Content\Style\TextStyle;
use PdfGenerator\Frontend\Layout\ContentBlock;
use PdfGenerator\Frontend\Layout\Flow;
use PdfGenerator\Frontend\Layout\Style\BlockStyle;
use PdfGenerator\Frontend\LinearDocument;
use PdfGenerator\Frontend\Resource\Font;
use PdfGenerator\IR\Document\Content\Common\Color;
use PHPUnit\Framework\TestCase;

class LinearDocumentTest extends TestCase
{
    public function testPrintRectangle()
    {
        // arrange
        $document = new LinearDocument([210, 297], [5, 5, 5, 5]);

        // act
        $rectangleStyle = new DrawingStyle();
        $rectangleStyle->setFillColor(new Color(0, 255, 0));
        $rectangleStyle->setLineColor(new Color(0, 255, 255));
        $rectangle = new Rectangle($rectangleStyle);

        $blockStyle = new BlockStyle();
        $blockStyle->setBackgroundColor(new Color(255, 0, 0));
        $blockStyle->setBorderColor(new Color(0, 0, 255));
        $blockStyle->setBorderWidth(1.0);
        $contentBlock = new ContentBlock($rectangle);
        $contentBlock->setStyle($blockStyle);
        $contentBlock->setMargin([20, 0, 0, 0]);
        $contentBlock->setPadding([5, 5, 5, 10]);
        $contentBlock->setWidth(40);
        $contentBlock->setHeight(40);
        $document->add($contentBlock);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('10', $result);
        $this->assertStringContainsString('20', $result);
        $this->assertStringContainsString('40', $result);
    }

    public function testPrintFlowRectangles()
    {
        // arrange
        $document = new LinearDocument();

        // act
        $rectangleStyle = new DrawingStyle(0.25);
        $flow = new Flow(Flow::DIRECTION_COLUMN);
        for ($i = 0; $i < 800; ++$i) {
            $rectangle = new Rectangle($rectangleStyle);
            $contentBlock = new ContentBlock($rectangle);
            $contentBlock->setWidth($i * 5 % 40);
            $contentBlock->setHeight($i * 3 % 17);
            $flow->add($contentBlock);
        }
        $outerFlow = new Flow();
        $outerFlow->add($flow);
        $document->add($outerFlow);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('10', $result);
        $this->assertStringContainsString('20', $result);
    }

    public function testPrintFlowText()
    {
        // arrange
        $document = new LinearDocument();

        $font = Font::createFromDefault();
        $normalText = new TextStyle($font);

        // act
        $flow = new Flow();
        for ($i = 0; $i < 10; ++$i) {
            $paragraph = new Paragraph();
            $paragraph->add($normalText, 'Hi mom!');
            $paragraph->add($normalText, ' Hi mom x fo real.');
            $paragraph->add($normalText, ' Exactly what I need ');
            $paragraph->add($normalText, ' Please allocate '.$i);
            $contentBlock = new ContentBlock($paragraph);
            $flow->add($contentBlock);
        }
        $document->add($flow);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('Hi mom', $result);
    }

    private function render(LinearDocument $document): string
    {
        $result = $document->save();
        file_put_contents('pdf.pdf', $result);

        return $result;
    }
}
