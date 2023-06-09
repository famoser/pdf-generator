<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Content\Style\Base;

class Style
{
    private float $marginTop;

    private float $marginBottom;

    public function getMarginTop(): float
    {
        return $this->marginTop;
    }

    public function setMarginTop(float $marginTop): void
    {
        $this->marginTop = $marginTop;
    }

    public function getMarginBottom(): float
    {
        return $this->marginBottom;
    }

    public function setMarginBottom(float $marginBottom): void
    {
        $this->marginBottom = $marginBottom;
    }
}
