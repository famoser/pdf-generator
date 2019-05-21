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
 * the GSUB table glyph substitutions.
 *
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/gsub
 */
class GSUBTable
{
    /*
     * the raw data.
     * is dependant of the glyph data
     */
    use RawContent;
}
