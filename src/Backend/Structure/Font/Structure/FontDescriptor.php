<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Font\Structure;

class FontDescriptor
{
    /**
     * all characters have same with.
     */
    const FLAG_FIXED_PITCH = 1;

    /**
     * contains serif (used in books).
     */
    const FLAG_SERIF = 2;

    /**
     * contains characters outside the Adobe standard latin range.
     */
    const FLAG_SYMBOLIC = 4;

    /**
     * glyphs resemble cursive handwriting.
     */
    const FLAG_SCRIPT = 8;

    /**
     * no characters outside the Adobe standard latin range.
     */
    const FLAG_NON_SYMBOLIC = 16;

    /**
     * glyphs have slanted vertical strokes.
     */
    const FLAG_ITALIC = 32;

    /**
     * no lowercase letters contained.
     */
    const FLAG_ALL_CAP = 131072; // 2^17

    /**
     * lowercase glyphs are like the uppercase glyphs but smaller.
     */
    const FLAG_SMALL_CAP = 262144; // 2^18

    /**
     * will advice readers to print extra pixels for lower text sizes.
     */
    const FLAG_FORCE_BOLD = 524288; //2^19

    /**
     * same value than from the referencing entry.
     *
     * @var string
     */
    private $fontName;

    /**
     * characteristics of the font.
     *
     * @var int
     */
    private $flags;

    /**
     * the max bounding box of all characters.
     *
     * @var int[]
     */
    private $fontBBox = [];

    /**
     * angle of the font
     * negative for slope to the right.
     *
     * @var int
     */
    private $italicAngle;

    /**
     * max height above baseline.
     *
     * @var int
     */
    private $ascent;

    /**
     * max depth below baseline.
     *
     * @var int
     */
    private $decent;

    /**
     * height of cap characters.
     *
     * @var int
     */
    private $capHeight;

    /**
     * thickness of dominant stems (de: stängel) of characters.
     *
     * @var int
     */
    private $stemV;
}
