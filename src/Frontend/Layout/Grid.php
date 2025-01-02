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
use Famoser\PdfGenerator\Frontend\LayoutEngine\ElementVisitorInterface;

class Grid extends AbstractElement
{
    use ColumnSizesTrait;

    /**
     * @var Row[]
     */
    private array $rows = [];

    /**
     * @param array<int, ColumnSize|string|float> $columnSizes
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
        $self = new self($this->gap, $this->perpendicularGap, $this->columnSizes);
        $self->rows = $rows;
        $self->writeStyle($this);

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
     * @param ElementVisitorInterface<T> $visitor
     *
     * @return T
     */
    public function accept(ElementVisitorInterface $visitor): mixed
    {
        return $visitor->visitGrid($this);
    }
}
