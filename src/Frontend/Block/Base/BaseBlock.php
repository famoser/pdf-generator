<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Block\Base;

abstract class BaseBlock
{
    /**
     * @var float[]
     */
    private array $margin;

    /**
     * @var float[]
     */
    private array $padding;

    private ?float $width;

    /**
     * @param float[] $margin
     * @param float[] $padding
     */
    public function __construct(array $margin = [0, 0, 0, 0], array $padding = [0, 0, 0, 0], float $width = null)
    {
        $this->margin = $margin;
        $this->padding = $padding;
        $this->width = $width;
    }

    /**
     * @param float[] $margin
     */
    public function setMargin(array $margin): self
    {
        $this->margin = $margin;

        return $this;
    }

    /**
     * @param float[] $padding
     */
    public function setPadding(array $padding): self
    {
        $this->padding = $padding;

        return $this;
    }

    public function setWidth(?float $width): BaseBlock
    {
        $this->width = $width;

        return $this;
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

    public function getWidth(): ?float
    {
        return $this->width;
    }
}
