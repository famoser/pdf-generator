<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR;

class Cursor
{
    public function __construct(private readonly float $xCoordinate, private readonly float $yCoordinate, private readonly int $pageIndex)
    {
    }

    public function getXCoordinate(): float
    {
        return $this->xCoordinate;
    }

    public function getYCoordinate(): float
    {
        return $this->yCoordinate;
    }

    public function getPageIndex(): int
    {
        return $this->pageIndex;
    }

    public function withXCoordinate(float $newXCoordinate): Cursor
    {
        return new self($newXCoordinate, $this->yCoordinate, $this->pageIndex);
    }

    public function withYCoordinate(float $newYCoordinate): Cursor
    {
        return new self($this->xCoordinate, $newYCoordinate, $this->pageIndex);
    }

    public function withPage(int $page): Cursor
    {
        return new self($this->xCoordinate, $this->yCoordinate, $page);
    }

    public function moveRight(float $right): self
    {
        $newXCoordinate = $this->getXCoordinate() + $right;

        return $this->withXCoordinate($newXCoordinate);
    }

    public function moveDown(float $down): self
    {
        $newYCoordinate = $this->getYCoordinate() - $down;

        return $this->withYCoordinate($newYCoordinate);
    }

    public function moveRightDown(float $right, float $down): self
    {
        $newXCoordinate = $this->getXCoordinate() + $right;
        $newYCoordinate = $this->getYCoordinate() - $down;

        return new self($newXCoordinate, $newYCoordinate, $this->getPageIndex());
    }

    public function isBiggerThan(self $other): bool
    {
        return $other->getPageIndex() < $this->getPageIndex() || ($other->getPageIndex() === $this->getPageIndex() && $other->getYCoordinate() < $this->getYCoordinate());
    }

    public function equals(self $start): bool
    {
        return $this->getPageIndex() === $start->getPageIndex()
            && $this->getYCoordinate() === $start->getYCoordinate()
            && $this->getXCoordinate() === $start->getXCoordinate();
    }
}
