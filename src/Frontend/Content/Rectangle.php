<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\Content;

use Famoser\PdfGenerator\Frontend\Content\Style\DrawingStyle;
use Famoser\PdfGenerator\Frontend\Printer;

readonly class Rectangle extends AbstractContent
{
    public function __construct(private float $width, private float $height, private DrawingStyle $style)
    {
        parent::__construct($this->width, $this->height);
    }

    public function getStyle(): DrawingStyle
    {
        return $this->style;
    }

    public function print(Printer $printer): void
    {
        $printer->printRectangle($this);
    }
}
