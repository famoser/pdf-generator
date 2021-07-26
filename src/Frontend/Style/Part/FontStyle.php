<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Style\Part;

class FontStyle
{
    private $name;
    public const STYLE_ITALIC = 'STYLE_ITALIC';
    public const WEIGHT_NORMAL = 'WEIGHT_NORMAL';
    public const WEIGHT_BOLD = 'WEIGHT_BOLD';
    private $src;
    private $style;
    private $weight;
}
