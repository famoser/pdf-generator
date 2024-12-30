<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\Content\Style;

use Famoser\PdfGenerator\Frontend\Layout\Style\BlockStyle;
use Famoser\PdfGenerator\IR\Document\Content\Common\Color;

readonly class DrawingStyle
{
    public function __construct(private ?float $lineWidth = 1, private ?Color $lineColor = new Color(0, 0, 0), private ?Color $fillColor = null)
    {
    }

    public static function createFromBlockStyle(BlockStyle $blockStyle): self
    {
        return new self(
            $blockStyle->getBorderWidth() ?? 0,
            $blockStyle->getBorderColor(),
            $blockStyle->getBackgroundColor()
        );
    }

    public function setLineWidth(?float $lineWidth): self
    {
        $this->lineWidth = $lineWidth;

        return $this;
    }

    public function setLineColor(?Color $lineColor): self
    {
        $this->lineColor = $lineColor;

        return $this;
    }

    public function setFillColor(?Color $fillColor): self
    {
        $this->fillColor = $fillColor;

        return $this;
    }

    public function getLineWidth(): ?float
    {
        return $this->lineWidth;
    }

    public function getLineColor(): ?Color
    {
        return $this->lineColor;
    }

    public function getFillColor(): ?Color
    {
        return $this->fillColor;
    }
}
