<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\LocatedContent\Base;

use PdfGenerator\Frontend\Position;
use PdfGenerator\Frontend\Size;

class LocatedContent
{
    private Position $position;

    private Size $size;

    /**
     * LocatedContent constructor.
     */
    public function __construct(Position $position, Size $size)
    {
        $this->position = $position;
        $this->size = $size;
    }

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function getSize(): Size
    {
        return $this->size;
    }
}
