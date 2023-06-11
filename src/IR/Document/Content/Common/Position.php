<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Document\Content\Common;

use PdfGenerator\FrontendResources\CursorPrinter\Cursor;

readonly class Position
{
    public function __construct(private float $startX, private float $startY)
    {
    }

    public static function fromCursor(Cursor $cursor): Position
    {
        return new self($cursor->getXCoordinate(), $cursor->getYCoordinate());
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
