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
    private ?float $borderWidth;
    private ?Color $borderColor;
    private ?Color $backgroundColor;
    private ?BlockSize $blockSize;

    /**
     * @param float[]|null $borderWidth
     */
    public function __construct(array $borderWidth = null, ?Color $borderColor = new Color(0, 0, 0), Color $backgroundColor = null, BlockSize $blockSize = BlockSize::INNER)
    {
        $this->borderWidth = $borderWidth;
        $this->borderColor = $borderColor;
        $this->backgroundColor = $backgroundColor;
        $this->blockSize = $blockSize;
    }

    public function setBorderWidth(?float $borderWidth): self
    {
        $this->borderWidth = $borderWidth;

        return $this;
    }

    public function setBorderColor(?Color $borderColor): self
    {
        $this->borderColor = $borderColor;

        return $this;
    }

    public function setBackgroundColor(?Color $backgroundColor): self
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    public function setBlockSize(?BlockSize $blockSize): BlockStyle
    {
        $this->blockSize = $blockSize;

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

    public function getBlockSize(): ?BlockSize
    {
        return $this->blockSize;
    }
}
