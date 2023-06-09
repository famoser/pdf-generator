<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Layout\Column;

use PdfGenerator\IR\Cursor;

class Column
{
    /**
     * Column constructor.
     */
    public function __construct(private Cursor $start, private float $width, private float $height)
    {
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

    public function getMaxLeft(): float
    {
        return $this->getWidth() + $this->getStart()->getXCoordinate();
    }

    public function getMinTop(): float
    {
        return $this->getStart()->getYCoordinate() - $this->getHeight();
    }

    public function getAvailableWidth(Cursor $cursor): float
    {
        return $this->getMaxLeft() - $cursor->getXCoordinate();
    }

    public function getAvailableHeight(Cursor $cursor): float
    {
        return $cursor->getYCoordinate() - $this->getMinTop();
    }

    public function hasHorizontalSpaceFor(Cursor $cursor, float $width): bool
    {
        if ($cursor->getXCoordinate() !== $this->start->getXCoordinate()) {
            $afterLeft = $cursor->getXCoordinate() + $width;
            $maxLeft = $this->getMaxLeft();
            if ($afterLeft > $maxLeft) {
                return false;
            }
        }

        return true;
    }

    public function hasVerticalSpaceFor(Cursor $cursor, float $height): bool
    {
        if ($cursor->getYCoordinate() !== $this->start->getYCoordinate()) {
            $afterTop = $cursor->getYCoordinate() - $height;
            $minTop = $this->getMinTop();
            if ($afterTop < $minTop) {
                return false;
            }
        }

        return true;
    }

    public function countSpaceFor(Cursor $cursor, float $height): float
    {
        $space = $cursor->getYCoordinate() - $this->getMinTop();

        return $space / $height;
    }
}
