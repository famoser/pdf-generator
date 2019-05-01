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
 * the horizontal metrics table defines how the horizontal font has to be rendered
 * the numberOfHMetrics field of the htmx table defines how many entries this table has.
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6hmtx.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/hmtx
 *
 * each entry defines the width and the offset to the left
 * if not an entry for each glyph exists, the last entry is used for the remaining glyphs
 */
class HMtxTable
{
}
