<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\Layout;

use Famoser\PdfGenerator\Frontend\Layout\Parts\Row;
use Famoser\PdfGenerator\Frontend\Layout\Style\ColumnSize;
use Famoser\PdfGenerator\Frontend\Layout\Traits\ColumnSizesTrait;
use Famoser\PdfGenerator\Frontend\LayoutEngine\BlockVisitorInterface;

class Grid extends AbstractBlock
{
    use ColumnSizesTrait;

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
     * @template T
     *
     * @param BlockVisitorInterface<T> $visitor
     *
     * @return T
     */
    public function accept(BlockVisitorInterface $visitor): mixed
    {
        return $visitor->visitGrid($this);
    }
}
