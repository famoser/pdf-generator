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
    /**
     * @var Column
     */
    private $column;

    /**
     * @var ColumnStyle
     */
    private $style;

    /**
     * @var AllocatorInterface[]|null
     */
    private $allocators = null;

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
        if ($this->allocators === null) {
            $this->allocators = [];
            foreach ($this->column->getBlocks() as $item) {
                $this->allocators[] = $item->createAllocator();
            }
        }

        return $this->allocators;
    }

    public function minimalWidth(): float
    {
        if ($this->style->getSizing() === ColumnStyle::SIZING_BY_WEIGHT) {
            return 0;
        }

        if ($this->style->getSizing() !== ColumnStyle::SIZING_BY_CONTENT) {
            throw new \Exception('Sizing ' . $this->style->getSizing() . ' not implemented');
        }

        $maxWidth = 0;
        foreach ($this->getAllocators() as $allocator) {
            $maxWidth = max($allocator->minimalWidth(), $maxWidth);
            $maxContentWidthEstimate = max($allocator->contentWidthEstimate(), $maxContentWidthEstimate);
        }

        return $maxWidth;
    }

    public function columnWidthEstimate(): ColumnWidthEstimate
    {
        $maxWidth = $this->minimalWidth();

        $maxContentWidthEstimate = 0;
        $totalContentWidth = 0;
        foreach ($this->getAllocators() as $allocator) {
            $contentWidthEstimate = $allocator->contentWidthEstimate();
            $totalContentWidth += $contentWidthEstimate;
            $maxContentWidthEstimate = max($contentWidthEstimate, $maxContentWidthEstimate);
        }
        $widthEstimateRelevance = $totalContentWidth / $maxContentWidthEstimate;

        return new ColumnWidthEstimate($maxWidth, $maxContentWidthEstimate, $widthEstimateRelevance);
    }

    public function place(float $maxWidth, float $maxHeight): array
    {
        // TODO: Implement place() method.
    }
}
