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
 * the cvt table lists values that can be referenced by instructions.
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6cvt.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/cvt
 */
class CvtTable
{
    /*
     * the raw data.
     */
    use RawContent;
}
