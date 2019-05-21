<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\File\Table;

use PdfGenerator\Font\Frontend\File\Traits\RawContent;

/**
 * the fpgm table contains instructions which are executed when using the font for the first time.
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6fpgm.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/fpgm
 */
class FpgmTable
{
    /*
     * the raw data.
     */
    use RawContent;
}
