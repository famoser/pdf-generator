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

use PdfGenerator\Frontend\Content\Style\DrawingStyle;
use PdfGenerator\Frontend\LayoutEngine\AbstractContentVisitor;

class Rectangle extends AbstractContent
{
    public function __construct(private DrawingStyle $style)
    {
    }

    public function setStyle(DrawingStyle $style): Rectangle
    {
        $this->style = $style;

        return $this;
    }

    public function getStyle(): DrawingStyle
    {
        return $this->style;
    }

    public function accept(AbstractContentVisitor $visitor): mixed
    {
        return $visitor->visitRectangle($this);
    }
}
