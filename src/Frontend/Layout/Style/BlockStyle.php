<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout\Style;

use PdfGenerator\IR\Document\Content\Common\Color;

class BlockStyle
{
    private ?float $borderWidth = null;
    private ?Color $borderColor = null;
    private ?Color $backgroundColor = null;

    public function setBorder(float $borderWidth, Color $borderColor = new Color(0, 0, 0)): self
    {
        $this->borderWidth = $borderWidth;
        $this->borderColor = $borderColor;

        return $this;
    }

    public function setBackgroundColor(?Color $backgroundColor): self
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
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
}
