<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\LayoutEngine\Place;

use PdfGenerator\Frontend\Layout\AbstractBlock;

readonly class BlockPlacement
{
    public function __construct(private float $width, private float $height, private ?AbstractBlock $overflow = null)
    {
    }

    public static function create(AbstractBlock $block, float $contentWidth, float $contentHeight, AbstractBlock $overflow = null): BlockPlacement
    {
        $width = $contentWidth + $block->getXSpace();
        $height = $contentHeight + $block->getYSpace();

        return new BlockPlacement($width, $height, $overflow);
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function getOverflow(): ?AbstractBlock
    {
        return $this->overflow;
    }
}
