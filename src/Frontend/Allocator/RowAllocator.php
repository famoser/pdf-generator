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
use PdfGenerator\Frontend\Block\Row;
use PdfGenerator\Frontend\Block\Style\RowStyle;

class RowAllocator extends BaseAllocator
{
    private Row $row;

    private RowStyle $rowStyle;

    /**
     * @var ColumnAllocator[]
     */
    private array $columnAllocators;

    /**
     * RowAllocator constructor.
     */
    public function __construct(Row $row)
    {
        $this->row = $row;
        $this->rowStyle = $row->getStyle();
    }

    /**
     * @return ColumnAllocator[]
     */
    private function getAllocators(): array
    {
        if (null === $this->columnAllocators) {
            $this->columnAllocators = [];
            foreach ($this->row->getColumns() as $item) {
                $this->columnAllocators[] = $item->createAllocator();
            }
        }

        return $this->columnAllocators;
    }

    public function minimalWidth(): float
    {
        $activeColumnWidth = 0;
        $widths = [];
        $presetColumnWidthCount = \count($this->rowStyle->getColumnWidths());
        foreach ($this->getAllocators() as $columnAllocator) {
            // preset wins if exists
            $presetColumnWidth = $activeColumnWidth < $presetColumnWidthCount ? $this->rowStyle->getColumnWidths()[$activeColumnWidth++] : null;
            if (null !== $presetColumnWidth) {
                $widths[] = $presetColumnWidth;
            } else {
                $widths[] = $columnAllocator->minimalWidth();
            }
        }

        $sum = array_sum($widths);
        $gutterWidth = max(0, \count($widths) - 1) * $this->rowStyle->getGutter();

        return $sum + $gutterWidth;
    }

    public function widthEstimate(): float
    {
        $activeColumnWidth = 0;
        $widths = [];
        $presetColumnWidthCount = \count($this->rowStyle->getColumnWidths());
        foreach ($this->getAllocators() as $columnAllocator) {
            // preset wins if exists
            $presetColumnWidth = $activeColumnWidth < $presetColumnWidthCount ? $this->rowStyle->getColumnWidths()[$activeColumnWidth++] : 0;
            $widthEstimate = $columnAllocator->widthEstimate();
            $widths[] = max($presetColumnWidth, $widthEstimate);
        }

        $sum = array_sum($widths);
        $gutterWidth = max(0, \count($widths) - 1) * $this->rowStyle->getGutter();

        return $sum + $gutterWidth;
    }

    /**
     * @return ColumnWidthEstimate[]
     */
    public function getColumnWidthEstimates(): array
    {
        $columnWidthEstimate = [];
        foreach ($this->getAllocators() as $allocator) {
            $columnWidthEstimate[] = $allocator->columnWidthEstimate();
        }

        return $columnWidthEstimate;
    }

    public function place(float $maxWidth)
    {
    }
}
