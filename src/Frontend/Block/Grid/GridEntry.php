<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Block\Grid;

use PdfGenerator\Frontend\Block\Base\Block;

class GridEntry
{
    private int $columnSpan;

    private int $rowSpan;

    private Block $block;

    /**
     * GridEntry constructor.
     */
    public function __construct(int $columnSpan, int $rowSpan, Block $block)
    {
        $this->columnSpan = $columnSpan;
        $this->rowSpan = $rowSpan;
        $this->block = $block;
    }

    public function getColumnSpan(): int
    {
        return $this->columnSpan;
    }

    public function getRowSpan(): int
    {
        return $this->rowSpan;
    }

    public function getBlock(): Block
    {
        return $this->block;
    }
}
