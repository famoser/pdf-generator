<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout\Grid;

use PdfGenerator\Frontend\Layout\Base\BaseBlock;

readonly class GridEntry
{
    public function __construct(private int $columnSpan, private int $rowSpan, private BaseBlock $block)
    {
    }

    public function getColumnSpan(): int
    {
        return $this->columnSpan;
    }

    public function getRowSpan(): int
    {
        return $this->rowSpan;
    }

    public function getBlock(): BaseBlock
    {
        return $this->block;
    }
}
