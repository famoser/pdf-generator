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

/**
 * the IS/2 table defines spacing & some more details for windows platform
 * its meant as a summary of the font; so after building subsets it needs to be rebuilt.
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6os2.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/os2
 */
class OS2Table
{
    /**
     * version of table.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $version;

    /**
     * average char width.
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $xAvgCharWidth;

    /**
     * weight classes.
     */

    /**
     * weight class ("boldness" of the font).
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $usWeightClass;

    /**
     * weight classes.
     */
    const WEIGHT_CLASS_THIN = 100;
    const WEIGHT_CLASS_EXTRALIGHT = 200;
    const WEIGHT_CLASS_LIGHT = 300;
    const WEIGHT_CLASS_NORMAL = 400;
    const WEIGHT_CLASS_MEDIUM = 500;
    const WEIGHT_CLASS_SEMIBOLD = 600;
    const WEIGHT_CLASS_BOLD = 700;
    const WEIGHT_CLASS_EXTRABOLD = 800;
    const WEIGHT_CLASS_BLACK = 900;

    /**
     * width class ("lengthness" of the font).
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $usWidthClass;

    /**
     * width classes.
     */
    const WIDTH_CLASS_ULTRA_CONDENSED = 1;
    const WIDTH_CLASS_EXTRA_CONDENSED = 2;
    const WIDTH_CLASS_CONDENSED = 3;
    const WIDTH_CLASS_SEMI_CONDENSED = 4;
    const WIDTH_CLASS_NORMAL = 5;
    const WIDTH_CLASS_SEMI_EXPANDED = 6;
    const WIDTH_CLASS_EXPANDED = 7;
    const WIDTH_CLASS_EXTRA_EXPANDED = 8;
    const WIDTH_CLASS_ULTRA_EXPANDED = 9;

    /**
     * font embedding licensing rights
     * restrictions up until bit 4 are mutually exclusive
     * other restrictions may be combined freely.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $fsType;

    /**
     * embedding possible; enduser has to acquire license.
     */
    const LICENSE_TYPE_EMBEDDING_INSTALLABLE = 0;

    /**
     * embedding only allowed if license to do so.
     */
    const LICENSE_TYPE_EMBEDDING_RESTRICTED = 2;

    /**
     * embedding only allowed for preview (resulting document must be read-only).
     */
    const LICENSE_TYPE_EMBEDDING_PREVIEW_PRINT = 4;

    /**
     * embedding possible.
     */
    const LICENSE_TYPE_EMBEDDING_EDITABLE = 8;

    /**
     * must not subset font.
     */
    const LICENSE_TYPE_SUBSETTING_DISALLOWED = 0x0100;

    /**
     * must only embedd bitmap (no outlines).
     */
    const LICENSE_TYPE_BITMAP_ONLY = 0x200;

    /**
     * the recommended width of a subscript character.
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $ySubscriptXSize;

    /**
     * the recommended height of a subscript character.
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $ySubscriptYSize;

    /**
     * the recommended horizontal offset of a subscript character.
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $ySubscriptXOffset;

    /**
     * the recommended vertical offset from the baseline of a subscript character.
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $ySubscriptYOffset;

    /**
     * the recommended width of a superscript character.
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $ySuperscriptXSize;

    /**
     * the recommended height of a superscript character.
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $ySuperscriptYSize;

    /**
     * the recommended horizontal offset of a superscript character.
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $ySuperscriptXOffset;

    /**
     * the recommended vertical offset from the baseline of a superscript character.
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $ySuperscriptYOffset;

    /**
     * the strikeout line width.
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $yStrikeoutSize;

    /**
     * the vertical offset from the baseline of the strikethrough line.
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $yStrikeoutPosition;

    /**
     * IBM classification of the font.
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $sFamilyClass;

    /**
     * panose classification number of the font.
     *
     * @ttf-type uint8[10]
     *
     * @var int[]
     */
    private $panose;

    /**
     * contained unicode blocks with each bit representing one.
     *
     * @ttf-type uint32[4]
     *
     * @var int[]
     */
    private $ulUnicodeRanges;

    /**
     * activated in first entry of @see ulUnicodeRanges when all chars inside range 0000-007F contained.
     */
    const UNICODE_RANGE_BASIC_LATIN = 1;

    /**
     * activated in first entry of @see ulUnicodeRanges when all chars inside range 0080-00FF contained.
     */
    const UNICODE_RANGE_LATIN_SUPPLEMENT = 2;

    /**
     * activated in first entry of @see ulUnicodeRanges when all chars inside range 0100-017F contained.
     */
    const UNICODE_RANGE_LATIN_EXTENDED_A = 4;

    /**
     * activated in first entry of @see ulUnicodeRanges when all chars inside range 0180-024F contained.
     */
    const UNICODE_RANGE_LATIN_EXTENDED_B = 8;

    /**
     * panose classification number of the font.
     *
     * @ttf-type char[4]
     *
     * @var string
     */
    private $achVendID;

    /**
     * font patterns (italics and sorts).
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $fsSelection;

    const FONT_SELECTION_ITALIC = 0;
    const FONT_SELECTION_UNDERSCORE = 2;
    const FONT_SELECTION_NEGATIVE = 4;
    const FONT_SELECTION_OUTLINED = 8;
    const FONT_SELECTION_STRIKEOUT = 16;
    const FONT_SELECTION_BOLD = 32;
    const FONT_SELECTION_REGULAR = 64;
    const FONT_SELECTION_USE_TYPO_METRICS = 128; // use metrics starting with sTypo instead of us
    const FONT_SELECTION_WWS = 256; // implies name id 16/17 valid WWS names and name id 21/22 are not included
    const FONT_SELECTION_OBLIQUE = 512;

    /**
     * min(minimum included unicode character, 0xFFFF).
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $usFirstCharIndex;

    /**
     * min(maximum included unicode character, 0xFFFF).
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $usLastCharIndex;

    /**
     * max height of character from baseline.
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $sTypoAscender;

    /**
     * max vertical extend of character below baseline.
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $sTypoDecenter;

    /**
     * the line gap (between max decender of line n down to max ascender of line n+1).
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $sTypoLineGap;

    /**
     * height above baseline for clipping region.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $usWinAscent;

    /**
     * vertical extend below baseline for clipping region.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $usWinDecent;

    /**
     * contained code pages blocks with each bit representing one.
     *
     * @ttf-type uint32[2]
     *
     * @var int[]
     */
    private $ulCodePageRanges;

    /**
     * code page 1252.
     */
    const CODE_PAGE_LATIN1 = 1;

    /**
     * height of non-ascending lower-case letter; for example the character x.
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $sxHeight;

    /**
     * height of upper-case letter; for example the character X.
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $sCapHeight;

    /**
     * unicode code point used as a placeholder for not available character
     * set to 0 to use glyph 0.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $usDefaultChar;

    /**
     * default break character; usually space (0x0020).
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $usBreakChar;

    /**
     * max count of characters needed to calculate their widths
     * if ligatures contained which use 3 characters, this number should be 3.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $usMaxContext;

    /**
     * point size lower bound the font is expected to be used with.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $usLowerOptimalPointSize;

    /**
     * point size upper bound the font is expected to be used with.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $usUpperOptimalPointSize;
}
