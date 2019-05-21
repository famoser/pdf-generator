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
 * the prep table contains instructions to be executed before each glyph is drawn.
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6prep.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/prep
 */
class PrepTable
{
    /*
     * the raw data.
     */
    use RawContent;
}
