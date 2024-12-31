<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\Layout\Style;

use Famoser\PdfGenerator\IR\Document\Content\Common\Color;

readonly class ElementStyle
{
    public function __construct(private ?float $borderWidth = null, private ?Color $borderColor = null, private ?Color $backgroundColor = null)
    {
    }

    public function getBorderWidth(): ?float
    {
        return $this->borderWidth;
    }

    public function getBorderColor(): ?Color
    {
        return $this->borderColor;
    }

    public function getBackgroundColor(): ?Color
    {
        return $this->backgroundColor;
    }

    public function hasImpact(): bool
    {
        $hasBorder = $this->getBorderWidth() && $this->getBorderColor();

        return $hasBorder || $this->getBackgroundColor();
    }
}
