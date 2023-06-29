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

use PdfGenerator\Frontend\Content\AbstractContent;

readonly class ContentAllocation
{
    public function __construct(private float $width, private float $height, private AbstractContent $content, private ?AbstractContent $overflow = null)
    {
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function getContent(): AbstractContent
    {
        return $this->content;
    }

    public function getOverflow(): ?AbstractContent
    {
        return $this->overflow;
    }
}
