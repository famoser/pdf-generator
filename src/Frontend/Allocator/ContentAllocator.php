<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Allocator;

use PdfGenerator\Frontend\Allocator\Content\ContentAllocatorInterface;
use PdfGenerator\Frontend\Block\Content;
use PdfGenerator\Frontend\Block\Style\ContentStyle;

class ContentAllocator implements AllocatorInterface
{
    private ContentStyle $style;

    private ContentAllocatorInterface $contentAllocator;

    /**
     * ContentAllocator constructor.
     */
    public function __construct(private Content $content)
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
