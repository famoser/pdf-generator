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

use Famoser\PdfGenerator\Frontend\Content\AbstractContent;
use Famoser\PdfGenerator\Frontend\Content\ImagePlacement;
use Famoser\PdfGenerator\Frontend\Content\Rectangle;
use Famoser\PdfGenerator\Frontend\Content\Style\DrawingStyle;
use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;
use Famoser\PdfGenerator\Frontend\Content\TextBlock;
use Famoser\PdfGenerator\Frontend\Layout\ContentBlock;
use Famoser\PdfGenerator\Frontend\Layout\Style\ElementStyle;
use Famoser\PdfGenerator\Frontend\LinearDocument;
use Famoser\PdfGenerator\Frontend\Resource\Font;
use Famoser\PdfGenerator\Frontend\Resource\Image;
use Famoser\PdfGenerator\IR\Document\Content\Common\Color;
use Famoser\PdfGenerator\Tests\Resources\ResourcesProvider;

class ContentTestCase extends LinearDocumentTestCase
{
    public function testPrintRectangle(): void
    {
        // arrange
        $document = new LinearDocument();

        // act
        $rectangleStyle = new DrawingStyle(lineColor: new Color(0, 255, 255), fillColor: new Color(0, 255, 0));
        $rectangle = new Rectangle(20, 40, $rectangleStyle);

        $contentBlock = $this->createHighlightedContentBlock($rectangle);
        $document->add($contentBlock);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('1 0 0 1 22 235 cm 1 w 0 0 1 RG 1 0 0 rg 0 0 20 40 re b', $result);
        $this->assertStringContainsString('1 0 0 1 5 5 cm 0 1 1 RG 0 1 0 rg 0 0 10 30 re b', $result);
    }

    public function testPrintImagePlacement(): void
    {
        // arrange
        $document = new LinearDocument();

        // act
        $image = Image::createFromFile(ResourcesProvider::getImage1Path());
        $imagePlacement = new ImagePlacement(30, 40, $image);

        $contentBlock = $this->createHighlightedContentBlock($imagePlacement);
        $document->add($contentBlock);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('20 0 0 30 5 5 cm /I Do', $result);
    }

    public function testPrintPhrase(): void
    {
        // arrange
        $document = new LinearDocument();

        // act
        $font = Font::createFromDefault();
        $normalText = new TextStyle($font, 3, 1.2, new Color(0, 0, 0));
        $paragraph = new TextBlock(200, 30);
        $paragraph->add($normalText, 'PDF ist ein Textformat, strukturiert ähnlich wie XML, einfach etwas weniger Struktur. ');

        $contentBlock = $this->createHighlightedContentBlock($paragraph);
        $document->add($contentBlock);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('1 0 0 1 22 260.68 cm 1 w 0 0 1 RG 1 0 0 rg 0 0 122.704 14.32 re b', $result);
        $this->assertStringContainsString('1 0 0 1 5 7.133 cm BT 0 0 0 rg /F 3 Tf 4.32 TL (PDF ist ein Textformat', $result);
    }

    public function testPrintPhrases(): void
    {
        // arrange
        $document = new LinearDocument();

        // act
        $font = Font::createFromDefault();
        $normalText = new TextStyle($font, 3, 2, new Color(0, 0, 0));
        $bigText = new TextStyle($font, 20, 1, new Color(0, 0, 0));
        $paragraph = new TextBlock();
        $paragraph->add($normalText, 'PDF ist ein Textformat, strukturiert ähnlich wie XML, einfach etwas weniger Struktur. ');
        $paragraph->add($bigText, 'Am besten einmal ein kleines PDF im ');
        $paragraph->add($normalText, 'Texteditor öffnen und durchschauen. Zum Beispiel vom Kontoauszug, diese PDFs haben oft etwas weniger komischer binary Anteil wie dies z.B. Tex generierte Dokumente haben.');

        $contentBlock = $this->createHighlightedContentBlock($paragraph);
        $document->add($contentBlock);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('1 0 0 1 22 178.6 cm', $result);
        $this->assertStringContainsString('1 -0 0 1 5 89.213 cm BT', $result);
    }

    private function createHighlightedContentBlock(AbstractContent $content, ?float $width = null, ?float $height = null): ContentBlock
    {
        $highlightBlockStyle = new ElementStyle(1.0, new Color(0, 0, 255), new Color(255, 0, 0));

        $contentBlock = new ContentBlock($content);
        $contentBlock->setStyle($highlightBlockStyle);
        $contentBlock->setMargin([7, 7, 7, 7]);
        $contentBlock->setPadding([5, 5, 5, 5]);

        $contentBlock->setWidth($width);
        $contentBlock->setHeight($height);

        return $contentBlock;
    }
}
