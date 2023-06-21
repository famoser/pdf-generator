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

use PdfGenerator\Frontend\Layout\Base\BaseBlock;

readonly class Allocation
{
    public function __construct(private float $width, private float $height, private ?BaseBlock $content, private bool $overflow)
    {
    }

    public static function createEmpty(): self
    {
        return new self(0, 0, null, true);
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function getContent(): ?BaseBlock
    {
        return $this->content;
    }

    public function hasOverflow(): bool
    {
        return $this->overflow;
    }
}
