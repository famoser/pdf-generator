<?php

namespace Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate\Allocators;

use Famoser\PdfGenerator\Frontend\Layout\Parts\Row;
use Famoser\PdfGenerator\Frontend\Layout\Table;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate\Allocation;

readonly class TableAllocator
{
    public function __construct(private float $width, private float $height)
    {
    }

    /**
     * @param Row[] $overflowBody
     *
     * @return Allocation[]
     */
    public function allocate(Table $table, array &$overflowBody = [], float &$usedWidth = 0, float &$usedHeight = 0): array
    {
        $columnSizes = $table->getNormalizedColumnSizes();

        $minimalRowAllocations = count($table->getHead()) + 1;
        $widthsPerColumn = [];
        $headerBlockAllocationsPerColumn = GridAllocator::allocatedBlocksPerColumn($table->getRows(), $columnSizes, $this->width, $this->height, $minimalRowAllocations, $widthsPerColumn);

        return GridAllocator::allocateRows($table->getRows(), $headerBlockAllocationsPerColumn, $widthsPerColumn, $this->height, 0, 0, $minimalRowAllocations, $overflowBody, $usedWidth, $usedHeight);
    }
}
