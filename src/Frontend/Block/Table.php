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
use PdfGenerator\Frontend\Block\Style\TableStyle;

class Table extends Block
{
    private TableStyle $style;

    /**
     * @var Row[]
     */
    private array $headerRows = [];

    /**
     * @var Row[]
     */
    private array $rows = [];

    /**
     * @param float[]|null $dimensions
     */
    public function __construct(TableStyle $style = null, array $dimensions = null)
    {
        parent::__construct($dimensions);

        $this->style = $style ?? new TableStyle();
    }

    public function addRow(Row $row): void
    {
        $this->rows[] = $row;
    }

    public function addHeaderRow(Row $row): void
    {
        $this->headerRows[] = $row;
    }

    public function getStyle(): TableStyle
    {
        return $this->style;
    }

    /**
     * @return Row[]
     */
    public function getHeaderRows(): array
    {
        return $this->headerRows;
    }

    /**
     * @return Row[]
     */
    public function getRows(): array
    {
        return $this->rows;
    }
}
