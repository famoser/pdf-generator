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

class Table extends AbstractElement
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
     * @param array<int, ColumnSize|string|float> $columnSizes
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
        $self = new self($this->columnSizes);
        $self->body = $body;
        $self->writeStyle($this);

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
     * @param ElementVisitorInterface<T> $visitor
     *
     * @return T
     */
    public function accept(ElementVisitorInterface $visitor): mixed
    {
        return $visitor->visitTable($this);
    }
}
