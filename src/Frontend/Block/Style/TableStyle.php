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

use PdfGenerator\Frontend\Block\Style\Part\Color;

class TableStyle extends RowStyle
{
    private readonly Color $rowDividerColor;

    public function __construct(array $columnWidths = null, private readonly float $rowDividerWidth = 0, Color $rowDividerColor = null, private readonly ?Color $alternatingBackgroundColor = null, private readonly bool $repeatHeader = false)
    {
        parent::__construct($columnWidths);
        $this->rowDividerColor = $rowDividerColor ?? Color::black();
    }

    public function getRowDividerWidth(): float
    {
        return $this->rowDividerWidth;
    }

    public function getRowDividerColor(): Color
    {
        return $this->rowDividerColor;
    }

    public function getAlternatingBackgroundColor(): ?Color
    {
        return $this->alternatingBackgroundColor;
    }

    public function getRepeatHeader(): bool
    {
        return $this->repeatHeader;
    }
}
