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
use PdfGenerator\IR\Structure\Document\Page\Content\Rectangle\RectangleStyle;

class RectangleAllocator implements ContentAllocatorInterface
{
    /**
     * @var Rectangle
     */
    private $rectangle;

    /**
     * @var RectangleStyle
     */
    private $style;

    /**
     * RectangleAllocator constructor.
     */
    public function __construct(Rectangle $rectangle)
    {
        $this->rectangle = $rectangle;
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
