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

use Famoser\PdfGenerator\Frontend\Content\Style\DrawingStyle;
use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;
use Famoser\PdfGenerator\Frontend\Content\Text\TextLine;
use Famoser\PdfGenerator\Frontend\Content\TextBlock;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate\Allocation;
use Famoser\PdfGenerator\Frontend\Resource\Font\FontRepository;
use Famoser\PdfGenerator\Frontend\Resource\Image\ImageRepository;
use Famoser\PdfGenerator\IR\Document;
use Famoser\PdfGenerator\IR\Document\Content\Common\Position;
use Famoser\PdfGenerator\IR\Document\Content\Common\Size;
use Famoser\PdfGenerator\IR\Document\Content\ImagePlacement;
use Famoser\PdfGenerator\IR\Document\Content\Text;
use Famoser\PdfGenerator\IR\Document\Content\Rectangle;
use Famoser\PdfGenerator\IR\Document\Content\Rectangle\RectangleStyle;
use Famoser\PdfGenerator\IR\Document\Page;

readonly class Printer
{
    public function __construct(private Document $document, private ImageRepository $imageRepository, private FontRepository $fontRepository, private Page $page, private float $left, private float $top)
    {
    }

    public function position(float $left, float $top): self
    {
        return new self($this->document, $this->imageRepository, $this->fontRepository, $this->page, $this->left + $left, $this->top + $top);
    }

    public function print(Allocation $allocation): void
    {
        $placedPrinter = self::position($allocation->getLeft(), $allocation->getTop());

        foreach ($allocation->getBlockAllocations() as $blockAllocation) {
            $placedPrinter->print($blockAllocation);
        }

        foreach ($allocation->getContent() as $content) {
            $content->print($placedPrinter);
        }
    }

    public function printImage(Content\ImagePlacement $imagePlacement): void
    {
        $IRImage = $this->imageRepository->getImage($imagePlacement->getImage());

        $position = $this->getPosition($imagePlacement->getHeight());
        $size = new Size($imagePlacement->getWidth(), $imagePlacement->getHeight());

        $imagePlacement = new ImagePlacement($IRImage, $position, $size);
        $this->page->addContent($imagePlacement);
    }

    public function printRectangle(Content\Rectangle $rectangle): void
    {
        $rectangleStyle = self::createRectangleStyle($rectangle->getStyle());

        $position = $this->getPosition($rectangle->getHeight());
        $size = new Size($rectangle->getWidth(), $rectangle->getHeight());

        $rectangle = new Rectangle($position, $size, $rectangleStyle);
        $this->page->addContent($rectangle);
    }

    public function printText(string $text, TextStyle $style): void
    {
        $position = $this->getPosition(0);

        $textStyle = self::createTextStyle($style, $style->getLeading(), 0);
        $segment = new Text\TextSegment($text, $textStyle);
        $line = new Text\TextLine(0, [$segment]);
        $paragraph = new Text([$line], $position);
        $this->page->addContent($paragraph);
    }

    public function printTextBlock(TextBlock $textBlock): void
    {
        $lines = [];
        foreach ($textBlock->getLines() as $line) {
            $segments = [];
            foreach ($line->getSegments() as $segment) {
                $textStyle = self::createTextStyle($segment->getTextStyle(), $line->getLeading(), $line->getWordSpacing());
                $segments[] = new Text\TextSegment($segment->getText(), $textStyle);
            }

            $lines[] = new Text\TextLine($line->getOffset(), $segments);
        }

        $position = $this->getPosition(0); // text is rendered as expected

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

    private function createTextStyle(TextStyle $textStyle, ?float $leading = null, float $wordSpace = 0): Text\TextStyle
    {
        $font = $this->fontRepository->getFont($textStyle->getFont());

        return new Text\TextStyle($font, $textStyle->getFontSize(), $leading ?? $textStyle->getLeading(), $wordSpace, $textStyle->getColor());
    }
}
