<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout\Content;

use PdfGenerator\Frontend\Layout\Content;
use PdfGenerator\Frontend\Layout\Content\Style\DrawingStyle;

class Rectangle extends Content
{
    public function __construct(private readonly DrawingStyle $style)
    {
        parent::__construct();
    }

    public function getStyle(): DrawingStyle
    {
        return $this->style;
    }
}
