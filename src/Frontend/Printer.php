<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend;

use PdfGenerator\Frontend\LayoutEngine\Allocate\BlockAllocation;
use PdfGenerator\Frontend\LayoutEngine\Place\ContentPlacementVisitor;
use PdfGenerator\IR\Document\Content\Common\Position;
use PdfGenerator\IR\Document\Content\Common\Size;
use PdfGenerator\IR\Document\Content\ImagePlacement;
use PdfGenerator\IR\Document\Content\Paragraph;
use PdfGenerator\IR\Document\Content\Rectangle;
use PdfGenerator\IR\Document\Content\Rectangle\RectangleStyle;
use PdfGenerator\IR\Document\Content\Text;
use PdfGenerator\IR\Document\Content\Text\TextStyle;
use PdfGenerator\IR\Document\Page;
use PdfGenerator\IR\Document\Resource\Image;

readonly class Printer
{
    public function __construct(private \PdfGenerator\IR\Document $document, private Page $page, private float $left, private float $top)
    {
    }

    public function position(float $left, float $top): self
    {
        return new self($this->document, $this->page, $this->left + $left, $this->top + $top);
    }

    public function printImage(Image $image, float $width, float $height): void
    {
        $position = $this->getPosition($height);
        $size = new Size($width, $height);

        $imagePlacement = new ImagePlacement($image, $position, $size);
        $this->page->addContent($imagePlacement);
    }

    public function printRectangle(float $width, float $height, RectangleStyle $rectangleStyle): void
    {
        $position = $this->getPosition($height);
        $size = new Size($width, $height);

        $rectangle = new Rectangle($position, $size, $rectangleStyle);
        $this->page->addContent($rectangle);
    }

    public function printText(string $text, float $height, TextStyle $textStyle): void
    {
        $position = $this->getPosition($height);

        $rectangle = new Text($text, $position, $textStyle);
        $this->page->addContent($rectangle);
    }

    /**
     * @param Text\Phrase[] $phrases
     */
    public function printPhrases(array $phrases, float $height): void
    {
        $position = $this->getPosition($height);

        $paragraph = new Paragraph($phrases, $position);
        $this->page->addContent($paragraph);
    }

    private function getPosition(float $height): Position
    {
        $top = $this->page->getSize()[1] - $this->top - $height;

        return new Position($this->left, $top);
    }

    public function print(BlockAllocation $allocation): void
    {
        $placedPrinter = self::position($allocation->getLeft(), $allocation->getTop());

        foreach ($allocation->getBlockAllocations() as $blockAllocation) {
            $placedPrinter->print($blockAllocation);
        }

        foreach ($allocation->getContentAllocations() as $contentAllocation) {
            $contentVisitor = new ContentPlacementVisitor($placedPrinter, $contentAllocation->getWidth(), $contentAllocation->getHeight());
            $contentAllocation->getContent()->accept($contentVisitor);
        }
    }
}
