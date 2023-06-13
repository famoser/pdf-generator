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

class Measurement
{
    /**
     * @param float|null $weight set by flowing content (e.g. text)
     *                           intuitively represents the approximate area covered by the element
     *                           typically include the minimal area measured, multiplied by a factor depending on how elastic the content flows
     * @param float|null $width  set by fixed-size content or if parent forces a height
     * @param float|null $height set by fixed-size content or if the parent forces a width
     */
    public function __construct(private readonly ?float $weight, private readonly ?float $width, private readonly ?float $height)
    {
    }

    public function getWeight(): ?float
    {
        return $this->weight;
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
