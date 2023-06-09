<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\MeasuredContent;

use PdfGenerator\Frontend\Allocator\Content\ContentAllocatorInterface;
use PdfGenerator\Frontend\Allocator\Content\RectangleAllocator;
use PdfGenerator\Frontend\Content\Style\DrawingStyle;
use PdfGenerator\Frontend\MeasuredContent\Base\MeasuredContent;

class Rectangle extends MeasuredContent
{
    /**
     * Rectangle constructor.
     */
    public function __construct(private readonly DrawingStyle $style, private readonly \PdfGenerator\Frontend\Content\Rectangle $rectangle)
    {
    }

    public function getStyle(): DrawingStyle
    {
        return $this->style;
    }

    public function getRectangle(): \PdfGenerator\Frontend\Content\Rectangle
    {
        return $this->rectangle;
    }

    public function getWidth(): float
    {
        return $this->rectangle->getWidth() + $this->style->getLineWidth();
    }

    public function createAllocator(): ContentAllocatorInterface
    {
        return new RectangleAllocator($this);
    }
}
