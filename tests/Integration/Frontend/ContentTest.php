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
use Famoser\PdfGenerator\Frontend\Document;
use Famoser\PdfGenerator\Frontend\Layout\AbstractElement;
use Famoser\PdfGenerator\Frontend\Layout\Block;
use Famoser\PdfGenerator\Frontend\Layout\ContentBlock;
use Famoser\PdfGenerator\Frontend\Layout\Style\ElementStyle;
use Famoser\PdfGenerator\Frontend\Layout\Text;
use Famoser\PdfGenerator\Frontend\Resource\Font;
use Famoser\PdfGenerator\Frontend\Resource\Image;
use Famoser\PdfGenerator\IR\Document\Content\Common\Color;
use Famoser\PdfGenerator\Tests\Integration\Frontend\TestUtils\Render;
use Famoser\PdfGenerator\Tests\Resources\ResourcesProvider;
use PHPUnit\Framework\TestCase;

class ContentTest extends TestCase
{
    use Render;

    public function testPrintRectangle(): void
    {
        // arrange
        $document = new Document();

        // act
        $rectangleStyle = new DrawingStyle(lineColor: new Color(0, 255, 255), fillColor: new Color(0, 255, 0));
        $rectangle = new Rectangle(20, 40, $rectangleStyle);

        $contentBlock = $this->createHighlightedContentBlock($rectangle);
        $document->add($contentBlock);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('1 0 0 1 22 225 cm 1 w 0 0 1 RG 1 0 0 rg 0 0 30 50 re b', $result);
        $this->assertStringContainsString('1 0 -0 1 5 5 cm 0 1 1 RG 0 1 0 rg 0 0 20 40 re b', $result);
    }

    public function testPrintImagePlacement(): void
    {
        // arrange
        $document = new Document();

        // act
        $image = Image::createFromFile(ResourcesProvider::getImage1Path());
        $imagePlacement = new ImagePlacement(30, 40, $image);

        $contentBlock = $this->createHighlightedContentBlock($imagePlacement);
        $document->add($contentBlock);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('30 0 -0 40 5 5 cm /I Do', $result);
    }

    public function testPrintTextAlign(): void
    {
        // arrange
        $document = new Document();
        $dummyText = 'This PDF has justified text, which means each line has exactly the same width. This result is reached by balancing the space in between the words; making it longer if else the last word on the line would not be fully to the right of the block of text.';

        // act
        $font = Font::createFromDefault();
        $normalText = new TextStyle($font, new Color(0, 0, 0));

        $text = new Text(alignment: Text\Alignment::ALIGNMENT_JUSTIFIED);
        $text->addSpan($dummyText, $normalText);
        $document->add($this->createHighlightedBlock($text));

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('1 0 0 1 22 257.48 cm 2 w 0 0 1 RG 1 0 0 rg 0 0 166 17.52 re b', $result);
        $this->assertStringContainsString('1 0 -0 1 1 12.724 cm BT 0 0 0 rg /F 4 Tf 0.065 Tw 5.76 TL (This PDF has j', $result);
        $this->assertStringContainsString('0.175 0 (reached by balancing the space', $result);
    }

    public function testPrintPhrases(): void
    {
        // arrange
        $document = new Document();

        // act
        $font = Font::createFromDefault();
        $style = new TextStyle($font);
        $text = new Text(alignment: Text\Alignment::ALIGNMENT_JUSTIFIED);
        $text->addSpan('PDF ist ein Textformat, strukturiert ähnlich wie XML, einfach etwas weniger Struktur. ', $style, 3);
        $text->addSpan('Am besten einmal ein kleines PDF im ', $style, 10);
        $text->addSpan('Texteditor öffnen und durchschauen. Zum Beispiel vom Kontoauszug, diese PDFs haben oft etwas weniger komischer binary Anteil wie dies z.B. Tex generierte Dokumente haben.', $style, 3);

        $contentBlock = $this->createHighlightedBlock($text);
        $document->add($contentBlock);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('1 0 0 1 1 26.09 cm BT 0 0 0 rg /F 3 Tf 0.228 Tw 14.4 TL (PDF ist ein Textformat', $result);
        $this->assertStringContainsString('4.32 TL 0.079 0 (durchsc', $result);
    }

    private function createHighlightedContentBlock(AbstractContent $content): ContentBlock
    {
        $highlightBlockStyle = new ElementStyle(1.0, new Color(0, 0, 255), new Color(255, 0, 0));

        $contentBlock = new ContentBlock($content);
        $contentBlock->setStyle($highlightBlockStyle);
        $contentBlock->setMargin([7, 7, 7, 7]);
        $contentBlock->setPadding([5, 5, 5, 5]);

        return $contentBlock;
    }

    private function createHighlightedBlock(AbstractElement $content): Block
    {
        $highlightBlockStyle = new ElementStyle(2.0, new Color(0, 0, 255), new Color(255, 0, 0));

        $contentBlock = new Block($content);
        $contentBlock->setStyle($highlightBlockStyle);
        $contentBlock->setMargin([7, 7, 7, 7]);
        $contentBlock->setPadding([1, 1, 1, 1]);

        return $contentBlock;
    }
}
