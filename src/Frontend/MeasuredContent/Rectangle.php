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

use PdfGenerator\Frontend\Content\Style\DrawingStyle;
use PdfGenerator\Frontend\MeasuredContent\Base\MeasuredContent;

class Rectangle extends MeasuredContent
{
    /**
     * @var DrawingStyle
     */
    private $style;

    /**
     * @var \PdfGenerator\Frontend\Content\Rectangle
     */
    private $rectangle;

    /**
     * Rectangle constructor.
     */
    public function __construct(DrawingStyle $style, \PdfGenerator\Frontend\Content\Rectangle $rectangle)
    {
        $this->style = $style;
        $this->rectangle = $rectangle;
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
}
