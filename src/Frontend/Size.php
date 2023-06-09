<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend;

class Size
{
    /**
     * Size constructor.
     */
    public function __construct(private float $width, private float $heigth)
    {
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getHeigth(): float
    {
        return $this->heigth;
    }
}
