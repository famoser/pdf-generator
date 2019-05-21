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
 * the GPOS table defines the position of glyphs when it gets complicated.
 *
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/gpos
 */
class GPOSTable
{
    /*
     * the raw data.
     * is dependant of the glyph data
     */
    use RawContent;
}
