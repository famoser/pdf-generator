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

class GridStyle extends BlockStyle
{
    public function __construct(private float $gutterColumn = 0, private float $gutterRow = 0)
    {
        parent::__construct();
    }

    public function getGutterColumn(): float
    {
        return $this->gutterColumn;
    }

    public function getGutterRow(): float
    {
        return $this->gutterRow;
    }
}
