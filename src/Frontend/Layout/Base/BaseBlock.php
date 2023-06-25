<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout\Base;

use PdfGenerator\Frontend\Layout\Content\Style\BlockStyle;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;

/**
 * @template T of BlockStyle
 */
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

    private ?float $height;

    /**
     * @param T       $style
     * @param float[] $margin
     * @param float[] $padding
     */
    public function __construct(private readonly mixed $style = new BlockStyle(), array $margin = [0, 0, 0, 0], array $padding = [0, 0, 0, 0], float $width = null, float $height = null)
    {
        $this->margin = $margin;
        $this->padding = $padding;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * @return T
     */
    public function getStyle(): BlockStyle
    {
        return $this->style;
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

    public function setWidth(?float $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function setHeight(?float $height): self
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return float[]
     */
    public function getMargin(): array
    {
        return $this->margin;
    }

    public function getXMargin(): float
    {
        return $this->margin[1] + $this->margin[3];
    }

    public function getYMargin(): float
    {
        return $this->margin[0] + $this->margin[2];
    }

    public function getLeftMargin(): float
    {
        return $this->margin[3];
    }

    public function getTopMargin(): float
    {
        return $this->margin[0];
    }

    /**
     * @return float[]
     */
    public function getPadding(): array
    {
        return $this->padding;
    }

    public function getXPadding(): float
    {
        return $this->padding[1] + $this->padding[3];
    }

    public function getYPadding(): float
    {
        return $this->padding[0] + $this->padding[2];
    }

    public function getLeftPadding(): float
    {
        return $this->padding[3];
    }

    public function getTopPadding(): float
    {
        return $this->padding[0];
    }

    public function getXSpace(): float
    {
        return $this->getXMargin() + $this->getXPadding();
    }

    public function getYSpace(): float
    {
        return $this->getYMargin() + $this->getYPadding();
    }

    public function getLeftSpace()
    {
        return $this->getLeftMargin() + $this->getLeftPadding();
    }

    public function getTopSpace()
    {
        return $this->getTopMargin() + $this->getTopPadding();
    }

    public function getWidth(): ?float
    {
        return $this->width;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    /**
     * @template T
     *
     * @param AbstractBlockVisitor<T> $visitor
     *
     * @return T
     */
    abstract public function accept(AbstractBlockVisitor $visitor): mixed;
}