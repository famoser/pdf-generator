<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Block;

use PdfGenerator\Frontend\Allocator\RowAllocator;
use PdfGenerator\Frontend\Block\Base\Block;
use PdfGenerator\Frontend\Block\Style\RowStyle;

class Row extends Block
{
    /**
     * @var RowStyle
     */
    private $style;

    /**
     * @var Column[]
     */
    private $columns = [];

    /**
     * @param float[]|null $dimensions
     */
    public function __construct(RowStyle $style = null, array $dimensions = null)
    {
        parent::__construct($dimensions);

        $this->style = $style ?? new RowStyle();
    }

    public function addColumn(Column $column)
    {
        $this->columns[] = $column;
    }

    public function setColumn(int $columnIndex, Column $column)
    {
        while (\count($this->columns) <= $columnIndex) {
            $this->columns[] = null;
        }

        $this->columns[$columnIndex] = $column;
    }

    public function getStyle(): RowStyle
    {
        return $this->style;
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    public function createAllocator(): RowAllocator
    {
        return new RowAllocator($this);
    }
}
