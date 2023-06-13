<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\FrontendResources\Allocator;

use PdfGenerator\Frontend\Layout\Block;
use PdfGenerator\Frontend\Layout\Style\ContentStyle;
use PdfGenerator\FrontendResources\Allocator\Content\ContentAllocatorInterface;

class ContentAllocator implements AllocatorInterface
{
    private readonly ContentStyle $style;

    private readonly ContentAllocatorInterface $contentAllocator;

    public function __construct(private readonly Block $content)
    {
        $this->style = $content->getStyle();

        $this->contentAllocator = $content->getMeasuredContent()->createAllocator();
    }

    public function minimalWidth(): float
    {
        return $this->style->getWhitespaceSide() + $this->contentAllocator->minimalWidth();
    }

    public function widthEstimate(): float
    {
        return $this->style->getWhitespaceSide() + $this->contentAllocator->widthEstimate();
    }
}
