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
     */
    private int $version;

    /**
     * average char width.
     *
     * @ttf-type int16
     */
    private int $xAvgCharWidth;

    /**
     * weight classes.
     */

    /**
     * weight class ("boldness" of the font).
     *
     * @ttf-type uint16
     */
    private int $usWeightClass;

    /**
     * weight classes.
     */
    public const WEIGHT_CLASS_THIN = 100;
    public const WEIGHT_CLASS_EXTRALIGHT = 200;
    public const WEIGHT_CLASS_LIGHT = 300;
    public const WEIGHT_CLASS_NORMAL = 400;
    public const WEIGHT_CLASS_MEDIUM = 500;
    public const WEIGHT_CLASS_SEMIBOLD = 600;
    public const WEIGHT_CLASS_BOLD = 700;
    public const WEIGHT_CLASS_EXTRABOLD = 800;
    public const WEIGHT_CLASS_BLACK = 900;

    /**
     * width class ("lengthness" of the font).
     *
     * @ttf-type uint16
     */
    private int $usWidthClass;

    /**
     * width classes.
     */
    public const WIDTH_CLASS_ULTRA_CONDENSED = 1;
    public const WIDTH_CLASS_EXTRA_CONDENSED = 2;
    public const WIDTH_CLASS_CONDENSED = 3;
    public const WIDTH_CLASS_SEMI_CONDENSED = 4;
    public const WIDTH_CLASS_NORMAL = 5;
    public const WIDTH_CLASS_SEMI_EXPANDED = 6;
    public const WIDTH_CLASS_EXPANDED = 7;
    public const WIDTH_CLASS_EXTRA_EXPANDED = 8;
    public const WIDTH_CLASS_ULTRA_EXPANDED = 9;

    /**
     * font embedding licensing rights
     * restrictions up until bit 4 are mutually exclusive
     * other restrictions may be combined freely.
     *
     * @ttf-type uint16
     */
    private int $fsType;

    /**
     * embedding possible; enduser has to acquire license.
     */
    public const LICENSE_TYPE_EMBEDDING_INSTALLABLE = 0;

    /**
     * embedding only allowed if license to do so.
     */
    public const LICENSE_TYPE_EMBEDDING_RESTRICTED = 2;

    /**
     * embedding only allowed for preview (resulting document must be read-only).
     */
    public const LICENSE_TYPE_EMBEDDING_PREVIEW_PRINT = 4;

    /**
     * embedding possible.
     */
    public const LICENSE_TYPE_EMBEDDING_EDITABLE = 8;

    /**
     * must not subset font.
     */
    public const LICENSE_TYPE_SUBSETTING_DISALLOWED = 0x0100;

    /**
     * must only embedd bitmap (no outlines).
     */
    public const LICENSE_TYPE_BITMAP_ONLY = 0x200;

    /**
     * the recommended width of a subscript character.
     *
     * @ttf-type int16
     */
    private int $ySubscriptXSize;

    /**
     * the recommended height of a subscript character.
     *
     * @ttf-type int16
     */
    private int $ySubscriptYSize;

    /**
     * the recommended horizontal offset of a subscript character.
     *
     * @ttf-type int16
     */
    private int $ySubscriptXOffset;

    /**
     * the recommended vertical offset from the baseline of a subscript character.
     *
     * @ttf-type int16
     */
    private int $ySubscriptYOffset;

    /**
     * the recommended width of a superscript character.
     *
     * @ttf-type int16
     */
    private int $ySuperscriptXSize;

    /**
     * the recommended height of a superscript character.
     *
     * @ttf-type int16
     */
    private int $ySuperscriptYSize;

    /**
     * the recommended horizontal offset of a superscript character.
     *
     * @ttf-type int16
     */
    private int $ySuperscriptXOffset;

    /**
     * the recommended vertical offset from the baseline of a superscript character.
     *
     * @ttf-type int16
     */
    private int $ySuperscriptYOffset;

    /**
     * the strikeout line width.
     *
     * @ttf-type int16
     */
    private int $yStrikeoutSize;

    /**
     * the vertical offset from the baseline of the strikethrough line.
     *
     * @ttf-type int16
     */
    private int $yStrikeoutPosition;

    /**
     * IBM classification of the font.
     *
     * @ttf-type int16
     */
    private int $sFamilyClass;

    /**
     * panose classification number of the font.
     *
     * @ttf-type uint8[10]
     *
     * @var int[]
     */
    private array $panose;

    /**
     * contained unicode blocks with each bit representing one.
     *
     * @ttf-type uint32[4]
     *
     * @var int[]
     */
    private array $ulUnicodeRanges;

    /**
     * activated in first entry of @see ulUnicodeRanges when all chars inside range 0000-007F contained.
     */
    public const UNICODE_RANGE_BASIC_LATIN = 1;

    /**
     * activated in first entry of @see ulUnicodeRanges when all chars inside range 0080-00FF contained.
     */
    public const UNICODE_RANGE_LATIN_SUPPLEMENT = 2;

    /**
     * activated in first entry of @see ulUnicodeRanges when all chars inside range 0100-017F contained.
     */
    public const UNICODE_RANGE_LATIN_EXTENDED_A = 4;

    /**
     * activated in first entry of @see ulUnicodeRanges when all chars inside range 0180-024F contained.
     */
    public const UNICODE_RANGE_LATIN_EXTENDED_B = 8;

    /**
     * panose classification number of the font.
     *
     * @ttf-type char[4]
     */
    private string $achVendID;

    /**
     * font patterns (italics and sorts).
     *
     * @ttf-type uint16
     */
    private int $fsSelection;

    public const FONT_SELECTION_ITALIC = 0;
    public const FONT_SELECTION_UNDERSCORE = 2;
    public const FONT_SELECTION_NEGATIVE = 4;
    public const FONT_SELECTION_OUTLINED = 8;
    public const FONT_SELECTION_STRIKEOUT = 16;
    public const FONT_SELECTION_BOLD = 32;
    public const FONT_SELECTION_REGULAR = 64;
    public const FONT_SELECTION_USE_TYPO_METRICS = 128; // use metrics starting with sTypo instead of us
    public const FONT_SELECTION_WWS = 256; // implies name id 16/17 valid WWS names and name id 21/22 are not included
    public const FONT_SELECTION_OBLIQUE = 512;

    /**
     * min(minimum included unicode character, 0xFFFF).
     *
     * @ttf-type uint16
     */
    private int $usFirstCharIndex;

    /**
     * min(maximum included unicode character, 0xFFFF).
     *
     * @ttf-type uint16
     */
    private int $usLastCharIndex;

    /**
     * max height of character from baseline.
     *
     * @ttf-type int16
     */
    private int $sTypoAscender;

    /**
     * max vertical extend of character below baseline.
     *
     * @ttf-type int16
     */
    private int $sTypoDecender;

    /**
     * the line gap (between max decender of line n down to max ascender of line n+1).
     *
     * @ttf-type int16
     */
    private int $sTypoLineGap;

    /**
     * height above baseline for clipping region.
     *
     * @ttf-type uint16
     */
    private int $usWinAscent;

    /**
     * vertical extend below baseline for clipping region.
     *
     * @ttf-type uint16
     */
    private int $usWinDecent;

    /**
     * contained code pages blocks with each bit representing one.
     *
     * @ttf-type uint32[2]
     *
     * @var int[]|null
     */
    private ?array $ulCodePageRanges;

    /**
     * code page 1252.
     */
    public const CODE_PAGE_LATIN1 = 1;

    /**
     * height of non-ascending lower-case letter; for example the character x.
     *
     * @ttf-type int16
     */
    private ?int $sxHeight;

    /**
     * height of upper-case letter; for example the character X.
     *
     * @ttf-type int16
     */
    private ?int $sCapHeight;

    /**
     * unicode code point used as a placeholder for not available character
     * set to 0 to use glyph 0.
     *
     * @ttf-type uint16
     */
    private ?int $usDefaultChar;

    /**
     * default break character; usually space (0x0020).
     *
     * @ttf-type uint16
     */
    private ?int $usBreakChar;

    /**
     * max count of characters needed to calculate their widths
     * if ligatures contained which use 3 characters, this number should be 3.
     *
     * @ttf-type uint16
     */
    private ?int $usMaxContext;

    /**
     * point size lower bound the font is expected to be used with.
     *
     * @ttf-type uint16
     */
    private ?int $usLowerOptimalPointSize;

    /**
     * point size upper bound the font is expected to be used with.
     *
     * @ttf-type uint16
     */
    private ?int $usUpperOptimalPointSize;

    public function getVersion(): int
    {
        return $this->version;
    }

    public function setVersion(int $version): void
    {
        $this->version = $version;
    }

    public function getXAvgCharWidth(): int
    {
        return $this->xAvgCharWidth;
    }

    public function setXAvgCharWidth(int $xAvgCharWidth): void
    {
        $this->xAvgCharWidth = $xAvgCharWidth;
    }

    public function getUsWeightClass(): int
    {
        return $this->usWeightClass;
    }

    public function setUsWeightClass(int $usWeightClass): void
    {
        $this->usWeightClass = $usWeightClass;
    }

    public function getUsWidthClass(): int
    {
        return $this->usWidthClass;
    }

    public function setUsWidthClass(int $usWidthClass): void
    {
        $this->usWidthClass = $usWidthClass;
    }

    public function getFsType(): int
    {
        return $this->fsType;
    }

    public function setFsType(int $fsType): void
    {
        $this->fsType = $fsType;
    }

    public function getYSubscriptXSize(): int
    {
        return $this->ySubscriptXSize;
    }

    public function setYSubscriptXSize(int $ySubscriptXSize): void
    {
        $this->ySubscriptXSize = $ySubscriptXSize;
    }

    public function getYSubscriptYSize(): int
    {
        return $this->ySubscriptYSize;
    }

    public function setYSubscriptYSize(int $ySubscriptYSize): void
    {
        $this->ySubscriptYSize = $ySubscriptYSize;
    }

    public function getYSubscriptXOffset(): int
    {
        return $this->ySubscriptXOffset;
    }

    public function setYSubscriptXOffset(int $ySubscriptXOffset): void
    {
        $this->ySubscriptXOffset = $ySubscriptXOffset;
    }

    public function getYSubscriptYOffset(): int
    {
        return $this->ySubscriptYOffset;
    }

    public function setYSubscriptYOffset(int $ySubscriptYOffset): void
    {
        $this->ySubscriptYOffset = $ySubscriptYOffset;
    }

    public function getYSuperscriptXSize(): int
    {
        return $this->ySuperscriptXSize;
    }

    public function setYSuperscriptXSize(int $ySuperscriptXSize): void
    {
        $this->ySuperscriptXSize = $ySuperscriptXSize;
    }

    public function getYSuperscriptYSize(): int
    {
        return $this->ySuperscriptYSize;
    }

    public function setYSuperscriptYSize(int $ySuperscriptYSize): void
    {
        $this->ySuperscriptYSize = $ySuperscriptYSize;
    }

    public function getYSuperscriptXOffset(): int
    {
        return $this->ySuperscriptXOffset;
    }

    public function setYSuperscriptXOffset(int $ySuperscriptXOffset): void
    {
        $this->ySuperscriptXOffset = $ySuperscriptXOffset;
    }

    public function getYSuperscriptYOffset(): int
    {
        return $this->ySuperscriptYOffset;
    }

    public function setYSuperscriptYOffset(int $ySuperscriptYOffset): void
    {
        $this->ySuperscriptYOffset = $ySuperscriptYOffset;
    }

    public function getYStrikeoutSize(): int
    {
        return $this->yStrikeoutSize;
    }

    public function setYStrikeoutSize(int $yStrikeoutSize): void
    {
        $this->yStrikeoutSize = $yStrikeoutSize;
    }

    public function getYStrikeoutPosition(): int
    {
        return $this->yStrikeoutPosition;
    }

    public function setYStrikeoutPosition(int $yStrikeoutPosition): void
    {
        $this->yStrikeoutPosition = $yStrikeoutPosition;
    }

    public function getSFamilyClass(): int
    {
        return $this->sFamilyClass;
    }

    public function setSFamilyClass(int $sFamilyClass): void
    {
        $this->sFamilyClass = $sFamilyClass;
    }

    /**
     * @return int[]
     */
    public function getPanose(): array
    {
        return $this->panose;
    }

    /**
     * @param int[] $panose
     */
    public function setPanose(array $panose): void
    {
        $this->panose = $panose;
    }

    /**
     * @return int[]
     */
    public function getUlUnicodeRanges(): array
    {
        return $this->ulUnicodeRanges;
    }

    /**
     * @param int[] $ulUnicodeRanges
     */
    public function setUlUnicodeRanges(array $ulUnicodeRanges): void
    {
        $this->ulUnicodeRanges = $ulUnicodeRanges;
    }

    public function getAchVendID(): string
    {
        return $this->achVendID;
    }

    public function setAchVendID(string $achVendID): void
    {
        $this->achVendID = $achVendID;
    }

    public function getFsSelection(): int
    {
        return $this->fsSelection;
    }

    public function setFsSelection(int $fsSelection): void
    {
        $this->fsSelection = $fsSelection;
    }

    public function getUsFirstCharIndex(): int
    {
        return $this->usFirstCharIndex;
    }

    public function setUsFirstCharIndex(int $usFirstCharIndex): void
    {
        $this->usFirstCharIndex = $usFirstCharIndex;
    }

    public function getUsLastCharIndex(): int
    {
        return $this->usLastCharIndex;
    }

    public function setUsLastCharIndex(int $usLastCharIndex): void
    {
        $this->usLastCharIndex = $usLastCharIndex;
    }

    public function getSTypoAscender(): int
    {
        return $this->sTypoAscender;
    }

    public function setSTypoAscender(int $sTypoAscender): void
    {
        $this->sTypoAscender = $sTypoAscender;
    }

    public function getSTypoDecender(): int
    {
        return $this->sTypoDecender;
    }

    public function setSTypoDecender(int $sTypoDecender): void
    {
        $this->sTypoDecender = $sTypoDecender;
    }

    public function getSTypoLineGap(): int
    {
        return $this->sTypoLineGap;
    }

    public function setSTypoLineGap(int $sTypoLineGap): void
    {
        $this->sTypoLineGap = $sTypoLineGap;
    }

    public function getUsWinAscent(): int
    {
        return $this->usWinAscent;
    }

    public function setUsWinAscent(int $usWinAscent): void
    {
        $this->usWinAscent = $usWinAscent;
    }

    public function getUsWinDecent(): int
    {
        return $this->usWinDecent;
    }

    public function setUsWinDecent(int $usWinDecent): void
    {
        $this->usWinDecent = $usWinDecent;
    }

    /**
     * @return int[]|null
     */
    public function getUlCodePageRanges(): ?array
    {
        return $this->ulCodePageRanges;
    }

    /**
     * @param int[]|null $ulCodePageRanges
     */
    public function setUlCodePageRanges(?array $ulCodePageRanges): void
    {
        $this->ulCodePageRanges = $ulCodePageRanges;
    }

    public function getSxHeight(): ?int
    {
        return $this->sxHeight;
    }

    public function setSxHeight(?int $sxHeight): void
    {
        $this->sxHeight = $sxHeight;
    }

    public function getSCapHeight(): ?int
    {
        return $this->sCapHeight;
    }

    public function setSCapHeight(?int $sCapHeight): void
    {
        $this->sCapHeight = $sCapHeight;
    }

    public function getUsDefaultChar(): ?int
    {
        return $this->usDefaultChar;
    }

    public function setUsDefaultChar(?int $usDefaultChar): void
    {
        $this->usDefaultChar = $usDefaultChar;
    }

    public function getUsBreakChar(): ?int
    {
        return $this->usBreakChar;
    }

    public function setUsBreakChar(?int $usBreakChar): void
    {
        $this->usBreakChar = $usBreakChar;
    }

    public function getUsMaxContext(): ?int
    {
        return $this->usMaxContext;
    }

    public function setUsMaxContext(?int $usMaxContext): void
    {
        $this->usMaxContext = $usMaxContext;
    }

    public function getUsLowerOptimalPointSize(): ?int
    {
        return $this->usLowerOptimalPointSize;
    }

    public function setUsLowerOptimalPointSize(?int $usLowerOptimalPointSize): void
    {
        $this->usLowerOptimalPointSize = $usLowerOptimalPointSize;
    }

    public function getUsUpperOptimalPointSize(): ?int
    {
        return $this->usUpperOptimalPointSize;
    }

    public function setUsUpperOptimalPointSize(?int $usUpperOptimalPointSize): void
    {
        $this->usUpperOptimalPointSize = $usUpperOptimalPointSize;
    }
}
