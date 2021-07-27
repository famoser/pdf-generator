<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Style;

use PdfGenerator\Frontend\Style\Part\Color;

class TableStyle extends RowStyle
{
    /**
     * @var float
     */
    private $rowDividerWidth;

    /**
     * @var Color
     */
    private $rowDividerColor;

    /**
     * @var Color|null
     */
    private $alternatingBackgroundColor;

    /**
     * @var bool
     */
    private $repeatHeader;

    public function __construct(array $columnWidths = null, float $rowDividerWidth = 0, ?Color $rowDividerColor = null, ?Color $alternatingBackgroundColor = null, bool $repeatHeader = false)
    {
        parent::__construct($columnWidths);

        $this->rowDividerWidth = $rowDividerWidth;
        $this->rowDividerColor = $rowDividerColor ?? Color::black();
        $this->alternatingBackgroundColor = $alternatingBackgroundColor;
        $this->repeatHeader = $repeatHeader;
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
