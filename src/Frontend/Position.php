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

class Position
{
    /**
     * Position constructor.
     */
    public function __construct(private float $left, private float $top)
    {
    }

    public function getLeft(): float
    {
        return $this->left;
    }

    public function getTop(): float
    {
        return $this->top;
    }
}
