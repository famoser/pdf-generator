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
 * the name table associates strings with the font for different languages.
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6name.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/name
 */
class NameTable
{
    /*
     * the raw name data.
     *
     * does not depend on the characters included in the font.
     * however, there is large space saving potential by removing unneeded strings.
     *
     * version string id 5 used to determine version by windows; not from head table.
     */
    use RawContent;
}
