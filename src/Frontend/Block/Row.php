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

use PdfGenerator\Frontend\Block\Base\Block;
use PdfGenerator\Frontend\Style\Base\BlockStyle;
use PdfGenerator\Frontend\Style\RowStyle;

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
     * @var int[]
     */
    private $columnSpans = [];

    public function __construct(RowStyle $style, array $dimensions = null)
    {
        parent::__construct($dimensions);

        $this->style = $style;
    }

    public function addColumn(Column $column, int $columnSpan = 1)
    {
        $this->columns[] = $column;
        $this->columnSpans[] = $columnSpan;
    }

    public function setColumn(int $columnIndex, Column $column, int $columnSpan = 1)
    {
        while (\count($this->columns) <= $columnIndex) {
            $this->columns[] = null;
            $this->columnSpans[] = null;
        }

        $this->columns[$columnIndex] = $column;
        $this->columnSpans[$columnIndex] = $columnSpan;
    }

    public function getStyle(): BlockStyle
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

    /**
     * @return int[]
     */
    public function getColumnSpans(): array
    {
        return $this->columnSpans;
    }
}
