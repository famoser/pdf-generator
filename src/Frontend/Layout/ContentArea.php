<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout;

use PdfGenerator\Frontend\Position;
use PdfGenerator\Frontend\Size;

class ContentArea
{
    private Position $start;

    private Size $size;

    /**
     * ContentArea constructor.
     */
    public function __construct(Position $start, Size $size)
    {
        $this->start = $start;
        $this->size = $size;
    }

    public function getStart(): Position
    {
        return $this->start;
    }

    public function getSize(): Size
    {
        return $this->size;
    }
}
