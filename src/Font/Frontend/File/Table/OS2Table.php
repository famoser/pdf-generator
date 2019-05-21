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
 * the OS/2 table contains metrics of the font.
 * It is not required by MacOSX.
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6os2.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/os2
 */
class OS2Table
{
    /*
     * the raw OS/2 data
     * does not depend to a big extend on the characters included in the font.
     */
    use RawContent;
}
