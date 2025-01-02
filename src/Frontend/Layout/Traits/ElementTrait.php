<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\Layout\Traits;

use Famoser\PdfGenerator\Frontend\Layout\AbstractElement;
use Famoser\PdfGenerator\Frontend\Layout\Block;
use Famoser\PdfGenerator\Frontend\Layout\Style\ElementStyle;

trait ElementTrait
{
    private ?ElementStyle $style = null;

    /**
     * @var float[]
     */
    private array $margin = [0, 0, 0, 0];

    /**
     * @var float[]
     */
    private array $padding = [0, 0, 0, 0];

    private ?float $width = null;

    private ?float $height = null;

    public function writeStyle(AbstractElement $source): void
    {
        $this->style = $source->style;
        $this->margin = $source->margin;
        $this->padding = $source->padding;
        $this->width = $source->width;
        $this->height = $source->height;
    }

    public function setStyle(ElementStyle $style): self
    {
        $this->style = $style;

        return $this;
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

    public function getStyle(): ?ElementStyle
    {
        return $this->style;
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

    public function getXMargin(): float
    {
        return $this->margin[0] + $this->margin[2];
    }

    public function getYMargin(): float
    {
        return $this->margin[1] + $this->margin[3];
    }

    public function getLeftMargin(): float
    {
        return $this->margin[0];
    }

    public function getTopMargin(): float
    {
        return $this->margin[1];
    }

    public function getXPadding(): float
    {
        return $this->padding[0] + $this->padding[2];
    }

    public function getYPadding(): float
    {
        return $this->padding[1] + $this->padding[3];
    }

    public function getLeftPadding(): float
    {
        return $this->padding[0];
    }

    public function getTopPadding(): float
    {
        return $this->padding[1];
    }

    public function getXSpace(): float
    {
        return $this->getXMargin() + $this->getXPadding();
    }

    public function getYSpace(): float
    {
        return $this->getYMargin() + $this->getYPadding();
    }

    public function getLeftSpace(): float
    {
        return $this->getLeftMargin() + $this->getLeftPadding();
    }

    public function getTopSpace(): float
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
}
