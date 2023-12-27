<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout\Traits;

trait PerpendicularGapTrait
{
    /**
     * @var float
     *
     * margin to place in between the items
     */
    private float $perpendicularGap;

    public function setPerpendicularGap(float $perpendicularGap): self
    {
        $this->perpendicularGap = $perpendicularGap;

        return $this;
    }

    public function getPerpendicularGap(): float
    {
        return $this->perpendicularGap;
    }
}
