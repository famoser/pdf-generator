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

    /**
     * @return Cursor
     */
    public function setX(float $startX)
    {
        return new self($startX, $this->getYCoordinate(), $this->getPage());
    }

    /**
     * @return bool
     */
    public function isBiggerThan(self $other)
    {
        return $other->getPage() < $this->getPage() || ($other->getPage() === $this->getPage() && $other->getYCoordinate() < $this->getYCoordinate());
    }

    /**
     * @return Cursor
     */
    public function setY(float $startY)
    {
        return new self($this->getXCoordinate(), $startY, $this->getPage());
    }
}
