<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Style;

use PdfGenerator\Frontend\Style\Base\CellStyle;

class TextStyle extends CellStyle
{
    private $color = null;
    private $fontSize = null;
    private $font = null;
}
