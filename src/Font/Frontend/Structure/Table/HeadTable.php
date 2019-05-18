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

/**
 * the header table contains meta-data about the font
 * has impact on various other table as sets some fundamental parameters.
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6head.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/head
 *
 * simple table which sets for example left-to-right & unitsPerEm
 * when writing, ensure this is done last to compute the checksum correctly
 */
class HeadTable
{
}
