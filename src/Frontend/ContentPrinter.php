<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend;

use Famoser\PdfGenerator\Frontend\Content\ContentVisitorInterface;
use Famoser\PdfGenerator\Frontend\Content\ImagePlacement;
use Famoser\PdfGenerator\Frontend\Content\Rectangle;
use Famoser\PdfGenerator\Frontend\Content\Style\DrawingStyle;
use Famoser\PdfGenerator\Frontend\Content\TextBlock;
use Famoser\PdfGenerator\Frontend\Resource\Font\FontRepository;
use Famoser\PdfGenerator\Frontend\Resource\Image\ImageRepository;
use Famoser\PdfGenerator\IR\Document;
use Famoser\PdfGenerator\IR\Document\Content\Common\Position;
use Famoser\PdfGenerator\IR\Document\Content\Common\Size;
use Famoser\PdfGenerator\IR\Document\Content\Rectangle\RectangleStyle;
use Famoser\PdfGenerator\IR\Document\Content\Text;
use Famoser\PdfGenerator\IR\Document\Page;

readonly class ContentPrinter implements ContentVisitorInterface
{
    public function __construct(private ImageRepository $imageRepository, private FontRepository $fontRepository, private Page $page, private float $left, private float $top)
    {
    }

    public function visitImagePlacement(ImagePlacement $imagePlacement): void
    {
        $IRImage = $this->imageRepository->getImage($imagePlacement->getImage());

        $position = $this->getPosition($imagePlacement->getHeight());
        $size = new Size($imagePlacement->getWidth(), $imagePlacement->getHeight());

        $imagePlacement = new Document\Content\ImagePlacement($IRImage, $position, $size);
        $this->page->addContent($imagePlacement);
    }

    public function visitRectangle(Rectangle $rectangle): void
    {
        $rectangleStyle = self::createRectangleStyle($rectangle->getStyle());

        $position = $this->getPosition($rectangle->getHeight());
        $size = new Size($rectangle->getWidth(), $rectangle->getHeight());

        $rectangle = new Document\Content\Rectangle($position, $size, $rectangleStyle);
        $this->page->addContent($rectangle);
    }

    public function visitTextBlock(TextBlock $textBlock): void
    {
        $lines = [];
        foreach ($textBlock->getLines() as $line) {
            $segments = [];
            foreach ($line->getSegments() as $segment) {
                $font = $this->fontRepository->getFont($segment->getTextStyle()->getFont());
                $wordSpacing = $segment->getFontMeasurement()->getSpaceWidth() * $line->getWordSpacing();
                $textStyle = new Text\TextStyle($font, $segment->getFontMeasurement()->getFontSize(), $line->getLeading(), $wordSpacing, $segment->getTextStyle()->getColor());
                $segments[] = new Text\TextSegment($segment->getText(), $textStyle);
            }

            $lines[] = new Text\TextLine($line->getOffset(), $segments);
        }

        $ascender = [] !== $textBlock->getLines() ? $textBlock->getLines()[0]->getAscender() : 0.0;
        $position = $this->getPosition($ascender);

        $paragraph = new Text($lines, $position);
        $this->page->addContent($paragraph);
    }

    private function getPosition(float $height): Position
    {
        $top = $this->page->getSize()[1] - $this->top - $height;

        return new Position($this->left, $top);
    }

    private static function createRectangleStyle(DrawingStyle $drawingStyle): RectangleStyle
    {
        return new RectangleStyle($drawingStyle->getLineWidth(), $drawingStyle->getLineColor(), $drawingStyle->getFillColor());
    }
}
