<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate;

use Famoser\PdfGenerator\Frontend\Content\ImagePlacement;
use Famoser\PdfGenerator\Frontend\Content\Paragraph;
use Famoser\PdfGenerator\Frontend\Content\Rectangle;
use Famoser\PdfGenerator\Frontend\Content\Spacer;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate\Allocators\ParagraphAllocator;
use Famoser\PdfGenerator\Frontend\LayoutEngine\ContentVisitorInterface;

/**
 * @implements ContentVisitorInterface<ContentAllocation>
 */
readonly class ContentAllocationVisitor implements ContentVisitorInterface
{
    public function __construct(private float $width, private float $height)
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
