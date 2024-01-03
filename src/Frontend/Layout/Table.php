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
use PdfGenerator\Frontend\Layout\Traits\ColumnSizesTrait;
use PdfGenerator\Frontend\LayoutEngine\BlockVisitorInterface;

class Table extends AbstractBlock
{
    use ColumnSizesTrait;

    /**
     * @var Row[]
     */
    private array $head = [];

    /**
     * @var Row[]
     */
    private array $body = [];

    /**
     * @param (float|ColumnSize)[] $columnSizes
     */
    public function __construct(private readonly array $columnSizes = [])
    {
    }

    public function addHead(Row $row): self
    {
        $this->head[] = $row;

        return $this;
    }

    public function addBody(Row $row): self
    {
        $this->body[] = $row;

        return $this;
    }

    /**
     * @return Row[]
     */
    public function getHead(): array
    {
        return $this->head;
    }

    /**
     * @return Row[]
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * @return Row[]
     */
    public function getRows(): array
    {
        return array_merge($this->head, $this->body);
    }

    /**
     * @param Row[] $body
     */
    public function cloneWithBody(array $body): self
    {
        $self = clone $this;
        $self->body = $body;

        return $self;
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
     * @param BlockVisitorInterface<T> $visitor
     *
     * @return T
     */
    public function accept(BlockVisitorInterface $visitor): mixed
    {
        return $visitor->visitTable($this);
    }
}
