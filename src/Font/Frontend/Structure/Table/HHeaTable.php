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
 * the horizontal header table defines how a horizontal font has to be rendered
 * for special characters, the htmx table may override settings.
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6hhea.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/hhea
 *
 * sets properties like how much angle the character is displayed with (for italic) and baseline properties
 */
class HHeaTable
{
}
