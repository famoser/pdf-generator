<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Allocator;

use PdfGenerator\Frontend\Allocator\Base\BaseAllocator;
use PdfGenerator\Frontend\Allocator\RowAllocator\ColumnWidthEstimate;
use PdfGenerator\Frontend\Block\Column;
use PdfGenerator\Frontend\Block\Style\ColumnStyle;

class ColumnAllocator extends BaseAllocator
{
    private Column $column;

    private ColumnStyle $style;

    /**
     * @var AllocatorInterface[]|null
     */
    private ?array $allocators = null;

    /**
     * ColumnAllocator constructor.
     */
    public function __construct(Column $column)
    {
        $this->column = $column;
        $this->style = $column->getStyle();
    }

    private function getAllocators(): array
    {
        if (null === $this->allocators) {
            $this->allocators = [];
            foreach ($this->column->getBlocks() as $item) {
                $this->allocators[] = $item->createAllocator();
            }
        }

        return $this->allocators;
    }

    public function minimalWidth(): float
    {
        if (ColumnStyle::SIZING_BY_WEIGHT === $this->style->getSizing()) {
            return 0;
        }

        \assert(ColumnStyle::SIZING_BY_CONTENT === $this->style->getSizing());

        $maxWidth = 0;
        foreach ($this->getAllocators() as $allocator) {
            $maxWidth = max($allocator->minimalWidth(), $maxWidth);
        }

        return $maxWidth + $this->style->getWhitespaceSide();
    }

    public function columnWidthEstimate(): ColumnWidthEstimate
    {
        $maxWidth = $this->minimalWidth();

        $maxContentWidthEstimate = 0;
        $totalContentWidth = 0;
        foreach ($this->getAllocators() as $allocator) {
            $contentWidthEstimate = $allocator->widthEstimate();
            $totalContentWidth += $contentWidthEstimate;
            $maxContentWidthEstimate = max($contentWidthEstimate, $maxContentWidthEstimate);
        }
        $widthEstimateRelevance = $totalContentWidth / $maxContentWidthEstimate;

        $widthEstimate = $maxContentWidthEstimate + $this->style->getWhitespaceSide();

        return new ColumnWidthEstimate($maxWidth, $widthEstimate, $widthEstimateRelevance);
    }
}
