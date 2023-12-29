<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integration\Frontend\LinearDocument;

use PdfGenerator\Frontend\Content\AbstractContent;
use PdfGenerator\Frontend\Content\ImagePlacement;
use PdfGenerator\Frontend\Content\Paragraph;
use PdfGenerator\Frontend\Content\Rectangle;
use PdfGenerator\Frontend\Content\Style\DrawingStyle;
use PdfGenerator\Frontend\Content\Style\TextStyle;
use PdfGenerator\Frontend\Layout\ContentBlock;
use PdfGenerator\Frontend\Layout\Style\BlockStyle;
use PdfGenerator\Frontend\LinearDocument;
use PdfGenerator\Frontend\Resource\Font;
use PdfGenerator\Frontend\Resource\Image;
use PdfGenerator\IR\Document\Content\Common\Color;
use PdfGenerator\Tests\Resources\ResourcesProvider;
use PHPUnit\Framework\TestCase;

class ContentTest extends TestCase
{
    public function testPrintRectangle()
    {
        // arrange
        $document = new LinearDocument();

        // act
        $rectangleStyle = new DrawingStyle();
        $rectangleStyle->setFillColor(new Color(0, 255, 0));
        $rectangleStyle->setLineColor(new Color(0, 255, 255));
        $rectangle = new Rectangle($rectangleStyle);

        $contentBlock = $this->createHighlightedContentBlock($rectangle, 20, 40);
        $document->add($contentBlock);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('20', $result);
        $this->assertStringContainsString('40', $result);
    }

    public function testPrintImagePlacement()
    {
        // arrange
        $document = new LinearDocument();

        // act
        $image = Image::createFromFile(ResourcesProvider::getImage1Path());
        $imagePlacement = new ImagePlacement($image);

        $contentBlock = $this->createHighlightedContentBlock($imagePlacement, 30, 40);
        $document->add($contentBlock);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('30', $result);
        $this->assertStringContainsString('40', $result);
    }

    public function testPrintPhrase()
    {
        // arrange
        $document = new LinearDocument();

        // act
        $font = Font::createFromDefault();
        $normalText = new TextStyle($font, 3, 1.2, new Color(0, 0, 0));
        $paragraph = new Paragraph();
        $paragraph->add($normalText, 'PDF ist ein Textformat, strukturiert ähnlich wie XML, einfach etwas weniger Struktur. ');

        $contentBlock = $this->createHighlightedContentBlock($paragraph);
        $document->add($contentBlock);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('PDF ist ein', $result);
    }

    private function createHighlightedContentBlock(AbstractContent $content, float $width = null, float $height = null): ContentBlock
    {
        $highlightBlockStyle = new BlockStyle();
        $highlightBlockStyle->setBackgroundColor(new Color(255, 0, 0));
        $highlightBlockStyle->setBorder(1.0, new Color(0, 0, 255));

        $contentBlock = new ContentBlock($content);
        $contentBlock->setStyle($highlightBlockStyle);
        $contentBlock->setMargin([7, 7, 7, 7]);
        $contentBlock->setPadding([5, 5, 5, 5]);

        $contentBlock->setWidth($width);
        $contentBlock->setHeight($height);

        return $contentBlock;
    }

    private function render(LinearDocument $document): string
    {
        $result = $document->save();
        file_put_contents('pdf.pdf', $result);

        return $result;
    }
}
