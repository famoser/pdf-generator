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

use PdfGenerator\IR\Document\Content\Common\Position;
use PdfGenerator\IR\Document\Content\Common\Size;
use PdfGenerator\IR\Document\Content\ImagePlacement;
use PdfGenerator\IR\Document\Content\Rectangle;
use PdfGenerator\IR\Document\Content\Rectangle\RectangleStyle;
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

    public function getOrCreateImage(string $imagePath, string $type): Image
    {
        return $this->document->getOrCreateImage($imagePath, $type);
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

    private function getPosition(float $height): Position
    {
        $top = $this->page->getSize()[1] - $this->top - $height;

        return new Position($this->left, $top);
    }
}
