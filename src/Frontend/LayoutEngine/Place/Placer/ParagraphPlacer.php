<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\LayoutEngine\Place\Placer;

use PdfGenerator\Frontend\Layout\Content\Paragraph;

class ParagraphPlacer
{
    public function __construct(private float $width, private float $height)
    {
    }

    public function place(Paragraph $paragraph)
    {
    }
}
