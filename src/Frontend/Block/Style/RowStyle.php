<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Block\Style;

use PdfGenerator\Frontend\Block\Style\Base\BlockStyle;

class RowStyle extends BlockStyle
{
    /**
     * RowStyle constructor.
     *
     * @param float[] $columnWidths
     */
    public function __construct(private float $gutter = 0, private array $columnWidths = [])
    {
        parent::__construct();
    }

    public function getGutter(): float
    {
        return $this->gutter;
    }

    /**
     * @return float[]
     */
    public function getColumnWidths(): array
    {
        return $this->columnWidths;
    }
}
