<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure2\Content\Rectangle;

use PdfGenerator\IR\Structure2\Content\Common\Color;

class Style
{
    /**
     * @var float
     */
    private $lineWidth;

    /**
     * @var Color|null
     */
    private $borderColor;

    /**
     * @var Color|null
     */
    private $fillColor;

    /**
     * Style constructor.
     *
     * @param float $lineWidth
     * @param Color $borderColor
     * @param Color $fillColor
     */
    public function __construct(float $lineWidth, ?Color $borderColor, ?Color $fillColor)
    {
        $this->borderColor = $borderColor;
        $this->fillColor = $fillColor;
        $this->lineWidth = $lineWidth;
    }

    /**
     * @return float
     */
    public function getLineWidth(): float
    {
        return $this->lineWidth;
    }

    /**
     * @return Color|null
     */
    public function getBorderColor(): ?Color
    {
        return $this->borderColor;
    }

    /**
     * @return Color|null
     */
    public function getFillColor(): ?Color
    {
        return $this->fillColor;
    }
}
