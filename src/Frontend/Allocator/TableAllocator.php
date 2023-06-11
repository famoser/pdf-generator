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
use PdfGenerator\Frontend\Block\Style\TableStyle;
use PdfGenerator\Frontend\Block\Table;

class TableAllocator extends BaseAllocator
{
    private bool $firstTime = true;

    /**
     * @var FlowAllocator[]|null
     */
    private ?array $rowAllocators = null;

    public function __construct(private readonly Table $table, private readonly TableStyle $tableStyle)
    {
    }

    /**
     * @return FlowAllocator[]
     */
    private function getAllocators(): array
    {
        if (null === $this->rowAllocators) {
            $this->rowAllocators = [];
            foreach ($this->table->getRows() as $item) {
                $this->rowAllocators[] = $item->createAllocator();
            }
        }

        return $this->rowAllocators;
    }

    public function minimalWidth(): float
    {
        $minimalWidth = 0;

        foreach ($this->getAllocators() as $allocator) {
            $minimalWidth += $allocator->minimalWidth();
        }

        return $minimalWidth;
    }

    public function widthEstimate(): float
    {
        $widthEstimate = 0;

        foreach ($this->getAllocators() as $allocator) {
            $widthEstimate += $allocator->widthEstimate();
        }

        return $widthEstimate;
    }
}
