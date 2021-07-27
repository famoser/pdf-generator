<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Content\Style;

use PdfGenerator\Frontend\Block\Style\Part\Color;

class DrawingStyle
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
     */
    public function __construct(float $lineWidth, ?Color $borderColor = null, ?Color $fillColor = null)
    {
        $this->borderColor = $borderColor ?? Color::black();
        $this->fillColor = $fillColor;
        $this->lineWidth = $lineWidth;
    }

    public function getLineWidth(): float
    {
        return $this->lineWidth;
    }

    public function getBorderColor(): ?Color
    {
        return $this->borderColor;
    }

    public function getFillColor(): ?Color
    {
        return $this->fillColor;
    }
}
