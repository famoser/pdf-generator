<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Font\TTF\Table;

/**
 * the index-to-location table stores the location of glyphs in the glyf table
 * the indexToLocFormat in the head table defines the format
 * the number of entries is stored in the mapx table.
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6loca.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/loca
 *
 * when writing, always use the short format as it saves space
 */
class LocaTable
{
}
