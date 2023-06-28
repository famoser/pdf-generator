<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\LayoutEngine\Allocate;

use PdfGenerator\Frontend\Layout\AbstractBlock;

readonly class BlockAllocation
{
    public function __construct(private float $width, private float $height, private ?AbstractBlock $content, private bool $overflow)
    {
    }

    public static function create(AbstractBlock $block, float $contentWidth, float $contentHeight, ?AbstractBlock $content, bool $overflow): self
    {
        $width = $contentWidth + $block->getXSpace();
        $height = $contentHeight + $block->getYSpace();

        return new self($width, $height, $content, $overflow);
    }

    public static function createEmpty(bool $overflow): self
    {
        return new self(0, 0, null, $overflow);
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function getContent(): ?AbstractBlock
    {
        return $this->content;
    }

    public function hasOverflow(): bool
    {
        return $this->overflow;
    }
}
