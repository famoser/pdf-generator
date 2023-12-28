<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\LayoutEngine\Allocate;

use PdfGenerator\Frontend\Content\ImagePlacement;
use PdfGenerator\Frontend\Content\Paragraph;
use PdfGenerator\Frontend\Content\Rectangle;
use PdfGenerator\Frontend\Content\Spacer;
use PdfGenerator\Frontend\LayoutEngine\AbstractContentVisitor;
use PdfGenerator\Frontend\LayoutEngine\Allocate\Allocators\ParagraphAllocator;

/**
 * @implements AbstractContentVisitor<ContentAllocation|null>
 */
class ContentAllocationVisitor extends AbstractContentVisitor
{
    public function __construct(private readonly float $width, private readonly float $height)
    {
    }

    public function visitSpacer(Spacer $spacer): ContentAllocation
    {
        return new ContentAllocation($this->width, $this->height, $spacer);
    }

    public function visitRectangle(Rectangle $rectangle): ContentAllocation
    {
        return new ContentAllocation($this->width, $this->height, $rectangle);
    }

    public function visitImagePlacement(ImagePlacement $imagePlacement): ContentAllocation
    {
        return new ContentAllocation($this->width, $this->height, $imagePlacement);
    }

    public function visitParagraph(Paragraph $paragraph): ContentAllocation
    {
        $paragraphAllocator = new ParagraphAllocator($this->width, $this->height);

        $usedHeight = 0;
        $usedWidth = 0;
        $allocated = $paragraphAllocator->allocate($paragraph, $overflow, $usedHeight, $usedWidth);

        return new ContentAllocation($usedWidth, $usedHeight, $allocated, $overflow);
    }
}
