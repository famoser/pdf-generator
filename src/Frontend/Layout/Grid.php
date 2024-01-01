<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout;

use PdfGenerator\Frontend\Layout\Parts\Row;
use PdfGenerator\Frontend\Layout\Style\ColumnSize;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;

class Grid extends AbstractBlock
{
    /**
     * @var Row[]
     */
    private array $rows = [];

    /**
     * @param array<string|float|ColumnSize> $columnSizes
     */
    public function __construct(private readonly float $gap = 0, private readonly float $perpendicularGap = 0, private readonly array $columnSizes = [])
    {
    }

    public function add(Row $row): self
    {
        $this->rows[] = $row;

        return $this;
    }

    /**
     * @return Row[]
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * @param Row[] $rows
     */
    public function cloneWithRows(array $rows): self
    {
        $self = clone $this;
        $self->rows = $rows;

        return $self;
    }

    public function getGap(): float
    {
        return $this->gap;
    }

    public function getPerpendicularGap(): float
    {
        return $this->perpendicularGap;
    }

    /**
     * @return (string|float|ColumnSize)[]
     */
    public function getColumnSizes(): array
    {
        return $this->columnSizes;
    }

    /**
     * @template T
     *
     * @param AbstractBlockVisitor<T> $visitor
     *
     * @return T
     */
    public function accept(AbstractBlockVisitor $visitor): mixed
    {
        return $visitor->visitGrid($this);
    }

    /**
     * returns all column sizes for all columns used in the grid.
     * if column size undefined for some column, defaults to AUTO.
     *
     * @return (float|string|ColumnSize)[]
     */
    public function getNormalizedColumnSizes(): array
    {
        $maxColumn = max(...array_keys($this->getColumnSizes()));
        foreach ($this->getRows() as $row) {
            $maxColumn = max($maxColumn, ...array_keys($row->getColumns()));
        }

        $columnSizes = array_fill(0, $maxColumn + 1, ColumnSize::AUTO);
        foreach ($this->getColumnSizes() as $index => $columnSize) {
            $columnSizes[$index] = $columnSize;
        }

        return $columnSizes;
    }
}
