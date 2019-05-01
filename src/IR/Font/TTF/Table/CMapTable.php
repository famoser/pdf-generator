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
 * the character map table maps character codes to glyph indices.
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6cmap.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/cmap
 * @see https://github.com/opentypejs/opentype.js/blob/master/src/tables/cmap.js#L84
 *
 * contains of multiple subtables, each defines a different encoding
 * when reading, support as many formats as possible; at least Windows format 4 and 12.
 * when writing, only need unicode platform (0) and unicode 2.0 encoding (4)
 */
class CMapTable
{
}
