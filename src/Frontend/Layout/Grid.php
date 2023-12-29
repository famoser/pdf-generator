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
     * @param (float|ColumnSize)[] $columnSizes
     */
    public function __construct(private readonly float $gap = 0, private readonly float $perpendicularGap = 0, private readonly array $columnSizes = [])
    {
    }

    public function add(Row $row): self
    {
        $this->rows[] = $row;

        return $this;
    }

    public function addEntries(array $blocks): self
    {
        $row = new Row();
        foreach ($blocks as $index => $block) {
            $row->set($index, $block);
        }

        return $this->add($row);
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
     * @return (float|ColumnSize)[]
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
}
