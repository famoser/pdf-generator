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
    /**
     * @var float
     */
    private $xCoordinate;

    /**
     * @var float
     */
    private $yCoordinate;

    /**
     * @var int
     */
    private $pageIndex;

    /**
     * Cursor constructor.
     */
    public function __construct(float $xCoordinate, float $yCoordinate, int $page)
    {
        $this->xCoordinate = $xCoordinate;
        $this->yCoordinate = $yCoordinate;
        $this->pageIndex = $page;
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

    public function withXCoordinate(float $newXCoodinate)
    {
        return new self($newXCoodinate, $this->yCoordinate, $this->pageIndex);
    }

    public function withYCoordinate(float $newYCoordinate)
    {
        return new self($this->xCoordinate, $newYCoordinate, $this->pageIndex);
    }

    public function withPage(int $page)
    {
        return new self($this->xCoordinate, $this->yCoordinate, $page);
    }

    /**
     * @return bool
     */
    public function isBiggerThan(self $other)
    {
        return $other->getPageIndex() < $this->getPageIndex() || ($other->getPageIndex() === $this->getPageIndex() && $other->getYCoordinate() < $this->getYCoordinate());
    }
}
