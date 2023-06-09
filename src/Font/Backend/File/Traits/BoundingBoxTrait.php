<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Backend\File\Traits;

trait BoundingBoxTrait
{
    /**
     * x of lower left corner of bounding box.
     *
     * @ttf-type int16
     */
    private int $xMin;

    /**
     * y of lower left corner of bounding box.
     *
     * @ttf-type int16
     */
    private int $yMin;

    /**
     * x of upper right corner of bounding box.
     *
     * @ttf-type int16
     */
    private int $xMax;

    /**
     * y of upper right corner of bounding box.
     *
     * @ttf-type int16
     */
    private int $yMax;

    public function getXMin(): int
    {
        return $this->xMin;
    }

    public function setXMin(int $xMin): void
    {
        $this->xMin = $xMin;
    }

    public function getYMin(): int
    {
        return $this->yMin;
    }

    public function setYMin(int $yMin): void
    {
        $this->yMin = $yMin;
    }

    public function getXMax(): int
    {
        return $this->xMax;
    }

    public function setXMax(int $xMax): void
    {
        $this->xMax = $xMax;
    }

    public function getYMax(): int
    {
        return $this->yMax;
    }

    public function setYMax(int $yMax): void
    {
        $this->yMax = $yMax;
    }
}
