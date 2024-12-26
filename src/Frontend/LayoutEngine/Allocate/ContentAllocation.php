<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate;

use Famoser\PdfGenerator\Frontend\Content\AbstractContent;
use Famoser\PdfGenerator\Frontend\Content\Rectangle;
use Famoser\PdfGenerator\Frontend\Content\Style\DrawingStyle;
use Famoser\PdfGenerator\Frontend\Layout\Style\BlockStyle;

readonly class ContentAllocation
{
    public function __construct(private float $width, private float $height, private AbstractContent $content, private ?AbstractContent $overflow = null)
    {
    }

    public static function createFromBlockStyle(float $width, float $height, ?BlockStyle $style): self
    {
        $drawingStyle = DrawingStyle::createFromBlockStyle($style);
        $rectangle = new Rectangle($drawingStyle);

        return new self($width, $height, $rectangle);
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
