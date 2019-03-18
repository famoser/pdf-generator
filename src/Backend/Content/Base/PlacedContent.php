<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Content\Base;

abstract class PlacedContent extends BaseContent
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
     * PlacedContent constructor.
     *
     * @param float $xCoordinate
     * @param float $yCoordinate
     */
    public function __construct(float $xCoordinate, float $yCoordinate)
    {
        $this->xCoordinate = $xCoordinate;
        $this->yCoordinate = $yCoordinate;
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
}
