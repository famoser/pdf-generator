<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Allocator\RowAllocator;

class ColumnWidthEstimate
{
    public function __construct(private readonly float $minimalWidth = 0, private readonly float $widthEstimate = 0, private readonly float $widthEstimateRelevance = 1)
    {
    }

    public function getMinimalWidth(): float
    {
        return $this->minimalWidth;
    }

    public function getWidthEstimate(): float
    {
        return $this->widthEstimate;
    }

    public function getWidthEstimateRelevance(): float
    {
        return $this->widthEstimateRelevance;
    }
}
