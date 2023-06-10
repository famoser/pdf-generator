<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Allocator\Content;

use PdfGenerator\Frontend\MeasuredContent\Rectangle;
use PdfGenerator\IR\Document\Content\Rectangle\RectangleStyle;

class RectangleAllocator implements ContentAllocatorInterface
{
    private readonly RectangleStyle $style;

    public function __construct(private readonly Rectangle $rectangle)
    {
        $this->style = $rectangle->getStyle();
    }

    public function minimalWidth(): float
    {
        return $this->widthEstimate();
    }

    public function widthEstimate(): float
    {
        return $this->rectangle->getWidth();
    }
}
