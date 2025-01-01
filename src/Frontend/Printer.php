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
use Famoser\PdfGenerator\Frontend\Content\Style\DrawingStyle;
use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;
use Famoser\PdfGenerator\Frontend\Content\Text\TextLine;
use Famoser\PdfGenerator\Frontend\Content\TextBlock;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate\Allocation;
use Famoser\PdfGenerator\Frontend\Resource\Font\FontRepository;
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

    public function printText(string $text, TextStyle $normalText)
    {

    }
}
