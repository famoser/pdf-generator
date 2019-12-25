<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Document\Page\Content\Common;

class Position
{
    /**
     * @var float
     */
    private $startX;

    /**
     * @var float
     */
    private $startY;

    /**
     * Position constructor.
     */
    public function __construct(float $startX, float $startY)
    {
        $this->startX = $startX;
        $this->startY = $startY;
    }

    public function getStartX(): float
    {
        return $this->startX;
    }

    public function getStartY(): float
    {
        return $this->startY;
    }
}
