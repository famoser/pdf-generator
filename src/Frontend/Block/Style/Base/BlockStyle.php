<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Block\Style\Base;

use PdfGenerator\Frontend\Block\Style\Part\Color;

class BlockStyle
{
    /**
     * @var float[]
     */
    private $margin = [0, 0, 0, 0];

    /**
     * @var float[]
     */
    private $padding = [0, 0, 0, 0];

    /**
     * @var float
     */
    private $borderWidth = 0;

    /**
     * @var Color
     */
    private $borderColor;

    /**
     * @var Color|null
     */
    private $backgroundColor = null;

    public function __construct()
    {
        $this->borderColor = Color::black();
    }

    /**
     * @return float[]
     */
    public function getMargin(): array
    {
        return $this->margin;
    }

    /**
     * @return float[]
     */
    public function getPadding(): array
    {
        return $this->padding;
    }

    /**
     * @return float
     */
    public function getBorderWidth()
    {
        return $this->borderWidth;
    }

    public function getBorderColor(): Color
    {
        return $this->borderColor;
    }

    public function getBackgroundColor(): ?Color
    {
        return $this->backgroundColor;
    }

    public function getWhitespaceSide()
    {
        return $this->margin[1] + $this->margin[3] + $this->padding[1] + $this->padding[3];
    }
}
