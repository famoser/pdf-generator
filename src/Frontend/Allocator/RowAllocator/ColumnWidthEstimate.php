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
    private float $minimalWidth;

    private float $widthEstimate;

    private float $widthEstimateRelevance;

    public function __construct(float $minimalWidth = 0, float $widthEstimate = 0, float $widthEstimateRelevance = 1)
    {
        $this->minimalWidth = $minimalWidth;
        $this->widthEstimate = $widthEstimate;
        $this->widthEstimateRelevance = $widthEstimateRelevance;
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
