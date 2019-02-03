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
     *
     * @param float $xCoordinate
     * @param float $yCoordinate
     * @param int $page
     */
    public function __construct(float $xCoordinate, float $yCoordinate, int $page)
    {
        $this->xCoordinate = $xCoordinate;
        $this->yCoordinate = $yCoordinate;
        $this->page = $page;
    }

    /**
     * @return float
     */
    public function getXCoordinate(): float
    {
        return $this->xCoordinate;
    }

    /**
     * @return float
     */
    public function getYCoordinate(): float
    {
        return $this->yCoordinate;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param float $startX
     *
     * @return Cursor
     */
    public function setX(float $startX)
    {
        return new self($startX, $this->getYCoordinate(), $this->getPage());
    }

    /**
     * @param Cursor $other
     *
     * @return bool
     */
    public function isBiggerThan(self $other)
    {
        return $other->getPage() < $this->getPage() || ($other->getPage() === $this->getPage() && $other->getYCoordinate() < $this->getYCoordinate());
    }

    /**
     * @param float $startY
     *
     * @return Cursor
     */
    public function setY(float $startY)
    {
        return new self($this->getXCoordinate(), $startY, $this->getPage());
    }
}
