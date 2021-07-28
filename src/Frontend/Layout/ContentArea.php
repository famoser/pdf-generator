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

use PdfGenerator\Frontend\Cursor;

class ContentArea
{
    /**
     * @var Cursor
     */
    private $start;

    /**
     * @var float
     */
    private $width;

    /**
     * @var float
     */
    private $height;

    /**
     * ContentArea constructor.
     */
    public function __construct(Cursor $start, float $width, float $height)
    {
        $this->start = $start;
        $this->width = $width;
        $this->height = $height;
    }

    public function getStart(): Cursor
    {
        return $this->start;
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
