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

use Famoser\PdfGenerator\Frontend\Content\AbstractContent;
use Famoser\PdfGenerator\Frontend\Content\ImagePlacement;
use Famoser\PdfGenerator\Frontend\Content\Rectangle;
use Famoser\PdfGenerator\Frontend\Content\Style\DrawingStyle;
use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;
use Famoser\PdfGenerator\Frontend\Layout\Text;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate\Allocation;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate\Allocators\TextAllocator;
use Famoser\PdfGenerator\Frontend\Resource\Font\FontRepository;
use Famoser\PdfGenerator\Frontend\Resource\Image;
use Famoser\PdfGenerator\Frontend\Resource\Image\ImageRepository;
use Famoser\PdfGenerator\IR\Document\Page;

readonly class Printer
{
    public function __construct(private ImageRepository $imageRepository, private FontRepository $fontRepository, private Page $page, private float $left, private float $top)
    {
    }

    public function position(float $left = 0, float $top = 0): self
    {
        return new self($this->imageRepository, $this->fontRepository, $this->page, $this->left + $left, $this->top + $top);
    }

    public function place(Allocation $allocation): void
    {
        $placedPrinter = self::position($allocation->getLeft(), $allocation->getTop());

        foreach ($allocation->getBlockAllocations() as $blockAllocation) {
            $placedPrinter->place($blockAllocation);
        }

        foreach ($allocation->getContent() as $content) {
            $placedPrinter->print($content);
        }
    }

    public function print(AbstractContent $content): void
    {
        $contentPrinter = new ContentPrinter($this->imageRepository, $this->fontRepository, $this->page, $this->left, $this->top);
        $content->accept($contentPrinter);
    }

    public function printImage(float $width, float $height, string $src): void
    {
        $image = Image::createFromFile($src);
        $imagePlacement = new ImagePlacement($width, $height, $image);
        $this->print($imagePlacement);
    }

    public function printRectangle(float $width, float $height, DrawingStyle $style): void
    {
        $rectangle = new Rectangle($width, $height, $style);
        $this->print($rectangle);
    }

    public function printText(string $value, TextStyle $style, float $fontSize = 4, float $lineHeight = 1.2): void
    {
        $text = new Text();
        $text->addSpan($value, $style, $fontSize, $lineHeight);

        $textAllocator = new TextAllocator();
        $content = $textAllocator->allocate($text);

        $this->print($content);
    }
}
