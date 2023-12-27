<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\LayoutEngine\Measure;

readonly class Measurement
{
    /**
     * @param float $weight    set by flowing content (e.g. text)
     *                         intuitively represents the approximate area covered by the element
     *                         typically include the minimal area measured, multiplied by a factor depending on how elastic the content flows
     * @param float $minWidth  the min width required to place content
     * @param float $minHeight the min height required to place content
     */
    public function __construct(private float $weight, private float $minWidth, private float $minHeight)
    {
    }

    public static function zero(): self
    {
        return new self(0, 0, 0);
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function getMinWidth(): float
    {
        return $this->minWidth;
    }

    public function getMinHeight(): float
    {
        return $this->minHeight;
    }
}
