<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\Structure\Table;

use PdfGenerator\Font\Frontend\Structure\Traits\RawContent;

/**
 * the gasp table defines the rasterization technique based on the ppem of the output device.
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6gasp.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/gasp
 */
class GaspTable
{
    /*
     * the raw data.
     */
    use RawContent;
}
