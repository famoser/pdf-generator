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

class Rectangle extends Content
{
    /**
     * @var DrawingStyle
     */
    private $style;

    /**
     * @var float
     */
    private $width;

    /**
     * @var float
     */
    private $height;

    /**
     * Rectangle constructor.
     */
    public function __construct(DrawingStyle $style, float $width, float $height)
    {
        $this->style = $style;
        $this->width = $width;
        $this->height = $height;
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
}
