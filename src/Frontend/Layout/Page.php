<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout;

class Page
{
    /**
     * Page constructor.
     *
     * @param float[] $dimensions
     */
    public function __construct(private array $dimensions = [210, 297])
    {
    }

    /**
     * @return float[]
     */
    public function getDimensions(): array
    {
        return $this->dimensions;
    }
}
