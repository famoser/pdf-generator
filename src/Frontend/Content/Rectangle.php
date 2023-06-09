<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Content;

use PdfGenerator\Frontend\Content\Base\Content;
use PdfGenerator\Frontend\Content\Style\DrawingStyle;
use PdfGenerator\Frontend\ContentVisitor;
use PdfGenerator\Frontend\MeasuredContent\Base\MeasuredContent;

class Rectangle extends Content
{
    /**
     * Rectangle constructor.
     */
    public function __construct(private readonly DrawingStyle $style, private readonly float $width, private readonly float $height)
    {
    }

    public function getStyle(): DrawingStyle
    {
        return $this->style;
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function accept(ContentVisitor $contentVisitor): MeasuredContent
    {
        return $contentVisitor->visitRectangle($this);
    }
}
