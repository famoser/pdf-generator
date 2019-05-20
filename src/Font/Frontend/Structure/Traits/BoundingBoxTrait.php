<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\Structure\Traits;

trait BoundingBoxTrait
{
    /**
     * x of lower left corner of bounding box.
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $xMin;

    /**
     * y of lower left corner of bounding box.
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $yMin;

    /**
     * x of upper right corner of bounding box.
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $xMax;

    /**
     * y of upper right corner of bounding box.
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $yMax;

    /**
     * @return int
     */
    public function getXMin(): int
    {
        return $this->xMin;
    }

    /**
     * @param int $xMin
     */
    public function setXMin(int $xMin): void
    {
        $this->xMin = $xMin;
    }

    /**
     * @return int
     */
    public function getYMin(): int
    {
        return $this->yMin;
    }

    /**
     * @param int $yMin
     */
    public function setYMin(int $yMin): void
    {
        $this->yMin = $yMin;
    }

    /**
     * @return int
     */
    public function getXMax(): int
    {
        return $this->xMax;
    }

    /**
     * @param int $xMax
     */
    public function setXMax(int $xMax): void
    {
        $this->xMax = $xMax;
    }

    /**
     * @return int
     */
    public function getYMax(): int
    {
        return $this->yMax;
    }

    /**
     * @param int $yMax
     */
    public function setYMax(int $yMax): void
    {
        $this->yMax = $yMax;
    }
}
