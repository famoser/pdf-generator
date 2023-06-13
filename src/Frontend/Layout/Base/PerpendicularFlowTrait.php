<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout\Base;

trait PerpendicularFlowTrait
{
    /**
     * @var float
     *
     * margin to place in between the items in the perpendicular direction
     * e.g. if direction is set to row, this places a margin on the y-axis (in between the rows)
     */
    private float $perpendicularGap;

    /**
     * @var float[]|null
     *
     * if not null, forces the dimensions perpendicular to the flow direction
     * e.g. if direction is set to row, this forces the height of the rows
     */
    private ?array $perpendicularDimensions;

    public function setPerpendicularGap(float $perpendicularGap): self
    {
        $this->perpendicularGap = $perpendicularGap;

        return $this;
    }

    /**
     * @param float[]|null $perpendicularDimensions
     */
    public function setPerpendicularDimensions(?array $perpendicularDimensions): self
    {
        $this->perpendicularDimensions = $perpendicularDimensions;

        return $this;
    }

    public function getPerpendicularGap(): float
    {
        return $this->perpendicularGap;
    }

    /**
     * @return float[]|null
     */
    public function getPerpendicularDimensions(): ?array
    {
        return $this->perpendicularDimensions;
    }
}
