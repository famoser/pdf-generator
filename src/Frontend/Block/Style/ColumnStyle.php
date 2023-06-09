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

class ColumnStyle extends BlockStyle
{
    public const SIZING_BY_CONTENT = 'SIZING_BY_CONTENT';
    public const SIZING_BY_WEIGHT = 'SIZING_BY_WEIGHT';

    public function __construct(float $gutter = 0, private string $sizing = self::SIZING_BY_WEIGHT, private int $sizingWeight = 1)
    {
        parent::__construct();

        $this->gutter = $gutter;
    }

    public function getSizing(): string
    {
        return $this->sizing;
    }

    public function getSizingWeight(): int
    {
        return $this->sizingWeight;
    }
}
