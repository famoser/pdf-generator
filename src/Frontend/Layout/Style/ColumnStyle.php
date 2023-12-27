<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout\Style;

use PdfGenerator\IR\Document\Content\Common\Color;

class ColumnStyle extends BlockStyle
{
    private float|ColumnSize $columnSize;

    public function __construct(ColumnSize|float $columnSize = ColumnSize::AUTO, float $borderWidth = null, ?Color $borderColor = new Color(0, 0, 0), Color $backgroundColor = null, BlockSize $blockSize = BlockSize::INNER)
    {
        parent::__construct($borderWidth, $borderColor, $backgroundColor, $blockSize);
        $this->columnSize = $columnSize;
    }

    public function getColumnSize(): ColumnSize|float
    {
        return $this->columnSize;
    }

    public function setColumnSize(ColumnSize|float $columnSize): void
    {
        $this->columnSize = $columnSize;
    }
}
