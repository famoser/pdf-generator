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
    private $page;

    /**
     * Cursor constructor.
     */
    public function __construct(float $xCoordinate, float $yCoordinate, int $page)
    {
        $this->xCoordinate = $xCoordinate;
        $this->yCoordinate = $yCoordinate;
        $this->page = $page;
    }

    public function getXCoordinate(): float
    {
        return $this->xCoordinate;
    }

    public function getYCoordinate(): float
    {
        return $this->yCoordinate;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setX(float $xCoordinate)
    {
        $this->xCoordinate = $xCoordinate;
    }

    public function setY(float $yCoordinate)
    {
        $this->yCoordinate = $yCoordinate;
    }

    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    /**
     * @return bool
     */
    public function isBiggerThan(self $other)
    {
        return $other->getPage() < $this->getPage() || ($other->getPage() === $this->getPage() && $other->getYCoordinate() < $this->getYCoordinate());
    }
}
