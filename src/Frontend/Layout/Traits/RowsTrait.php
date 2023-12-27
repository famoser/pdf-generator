<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout\Traits;

use PdfGenerator\Frontend\Layout\Parts\Row;

trait RowsTrait
{
    /**
     * @var Row[]
     */
    private array $rows = [];

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
}
