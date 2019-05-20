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

use PdfGenerator\Font\Frontend\File\LongDateTime;
use PdfGenerator\Font\Frontend\Structure\Traits\BoundingBoxTrait;

/**
 * the header table contains meta-data about the font
 * has impact on various other table as sets some fundamental parameters.
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6head.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/head
 *
 * simple table which sets for example left-to-right & unitsPerEm
 * when writing, ensure this is done last to compute the checksum correctly
 *
 * set the flag 5-10,15 to 0 as these are not part of OpenType and might alter behaviour in other formats
 */
class HeadTable
{
    /**
     * baseline for font at y=0
     * must be set for variable fonts.
     */
    const FLAG_BASELINE_AT_0 = 1;

    /**
     * left sidebearing point at x=0.
     */
    const FLAG_LEFTBEARING_POINT_AT_0 = 2;

    /**
     * instructions may depend on font size.
     */
    const FLAG_INSTRUCTION_DEPENDENT_ON_POINT_SIZE = 4;

    /**
     * must use integer values for scaler math (else could use fractional).
     */
    const FLAG_FORCE_INTEGER_PPEM_VALUES = 8;

    /**
     * instructions may alter the advance width
     * must be set for variable fonts.
     */
    const FLAG_INSTRUCTIONS_ALTER_WIDTH = 16;

    /**
     * the binary layout of the file has changed but original functionality was retained.
     */
    const FLAG_TRANSFORMED = 2048;

    /**
     * the font has been converted.
     */
    const FLAG_CONVERTED = 4056;

    /**
     * optimized for clear type.
     */
    const FLAG_CLEAR_TYPE_OPTIMIZED = 8092;

    /**
     * font is a last resort font
     * as a result, the cmap table behaves differently.
     */
    const FLAG_LAST_RESORT_FONT = 14;

    /**
     * the styles of the font.
     */
    const MAC_STYLE_BOLD = 1;
    const MAC_STYLE_ITALIC = 2;
    const MAC_STYLE_UNDERLINE = 4;
    const MAC_STYLE_OUTLINE = 8;
    const MAC_STYLE_SHADOW = 16;
    const MAC_STYLE_CONDENSED = 32;
    const MAC_STYLE_EXTENDED = 64;

    /**
     * in which way the font should be read
     * for "normal" roman fonts the value is 2 because of punctuation/neutral characters.
     */
    const FONT_DIRECTION_RIGHT_TO_LEFT_WITH_NEUTRALS = -2;
    const FONT_DIRECTION_RIGHT_TO_LEFT = -1;
    const FONT_DIRECTION_MIXED = 0;
    const FONT_DIRECTION_LEFT_TO_RIGHT = 1;
    const FONT_DIRECTION_LEFT_TO_RIGHT_WITH_NEUTRALS = 2;

    /**
     * major version of this font.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $majorVersion;

    /**
     * minor version of this font.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $minorVersion;

    /**
     * font revision set by producer
     * ignored by microsoft, instead uses the version from the name table.
     *
     * @ttf-type fixed
     *
     * @var float
     */
    private $fontRevision;

    /**
     * check sum adjusted.
     *
     * @ttf-type uint32
     *
     * @var int
     */
    private $checkSumAdjustment;

    /**
     * magic number set to 0x5F0F3CF5.
     *
     * @ttf-type uint32
     *
     * @var int
     */
    private $magicNumber;

    /**
     * flags of this font.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $flags;

    /**
     * how many units used per em
     * 16-16384 is allowed; recommended are powers of 2.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $unitsPerEm;

    /**
     * when the font was created.
     *
     * @ttf-type LONGDATETIME
     *
     * @var LongDateTime
     */
    private $created;

    /**
     * when the font was last modified.
     *
     * @ttf-type LONGDATETIME
     *
     * @var LongDateTime
     */
    private $modified;

    use BoundingBoxTrait;

    /**
     * the style of the font.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $macStyle;

    /**
     * smallest readable size in pixels.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $lowestRecPPEM;

    /**
     * in which way the characters should be read.
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $fontDirectionHints;

    /**
     * which format the Loca table is in
     * 0 for short offset16[]
     * 1 for long offset32[].
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $indexToLocFormat;

    /**
     * which format the glyf table is in
     * 0 for current format.
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $glyphDataFormat;

    /**
     * @return int
     */
    public function getMajorVersion(): int
    {
        return $this->majorVersion;
    }

    /**
     * @param int $majorVersion
     */
    public function setMajorVersion(int $majorVersion): void
    {
        $this->majorVersion = $majorVersion;
    }

    /**
     * @return int
     */
    public function getMinorVersion(): int
    {
        return $this->minorVersion;
    }

    /**
     * @param int $minorVersion
     */
    public function setMinorVersion(int $minorVersion): void
    {
        $this->minorVersion = $minorVersion;
    }

    /**
     * @return float
     */
    public function getFontRevision(): float
    {
        return $this->fontRevision;
    }

    /**
     * @param float $fontRevision
     */
    public function setFontRevision(float $fontRevision): void
    {
        $this->fontRevision = $fontRevision;
    }

    /**
     * @return int
     */
    public function getCheckSumAdjustment(): int
    {
        return $this->checkSumAdjustment;
    }

    /**
     * @param int $checkSumAdjustment
     */
    public function setCheckSumAdjustment(int $checkSumAdjustment): void
    {
        $this->checkSumAdjustment = $checkSumAdjustment;
    }

    /**
     * @return int
     */
    public function getMagicNumber(): int
    {
        return $this->magicNumber;
    }

    /**
     * @param int $magicNumber
     */
    public function setMagicNumber(int $magicNumber): void
    {
        $this->magicNumber = $magicNumber;
    }

    /**
     * @return int
     */
    public function getFlags(): int
    {
        return $this->flags;
    }

    /**
     * @param int $flags
     */
    public function setFlags(int $flags): void
    {
        $this->flags = $flags;
    }

    /**
     * @return int
     */
    public function getUnitsPerEm(): int
    {
        return $this->unitsPerEm;
    }

    /**
     * @param int $unitsPerEm
     */
    public function setUnitsPerEm(int $unitsPerEm): void
    {
        $this->unitsPerEm = $unitsPerEm;
    }

    /**
     * @return LongDateTime
     */
    public function getCreated(): LongDateTime
    {
        return $this->created;
    }

    /**
     * @param LongDateTime $created
     */
    public function setCreated(LongDateTime $created): void
    {
        $this->created = $created;
    }

    /**
     * @return LongDateTime
     */
    public function getModified(): LongDateTime
    {
        return $this->modified;
    }

    /**
     * @param LongDateTime $modified
     */
    public function setModified(LongDateTime $modified): void
    {
        $this->modified = $modified;
    }

    /**
     * @return int
     */
    public function getMacStyle(): int
    {
        return $this->macStyle;
    }

    /**
     * @param int $macStyle
     */
    public function setMacStyle(int $macStyle): void
    {
        $this->macStyle = $macStyle;
    }

    /**
     * @return int
     */
    public function getLowestRecPPEM(): int
    {
        return $this->lowestRecPPEM;
    }

    /**
     * @param int $lowestRecPPEM
     */
    public function setLowestRecPPEM(int $lowestRecPPEM): void
    {
        $this->lowestRecPPEM = $lowestRecPPEM;
    }

    /**
     * @return int
     */
    public function getFontDirectionHints(): int
    {
        return $this->fontDirectionHints;
    }

    /**
     * @param int $fontDirectionHints
     */
    public function setFontDirectionHints(int $fontDirectionHints): void
    {
        $this->fontDirectionHints = $fontDirectionHints;
    }

    /**
     * @return int
     */
    public function getIndexToLocFormat(): int
    {
        return $this->indexToLocFormat;
    }

    /**
     * @param int $indexToLocFormat
     */
    public function setIndexToLocFormat(int $indexToLocFormat): void
    {
        $this->indexToLocFormat = $indexToLocFormat;
    }

    /**
     * @return int
     */
    public function getGlyphDataFormat(): int
    {
        return $this->glyphDataFormat;
    }

    /**
     * @param int $glyphDataFormat
     */
    public function setGlyphDataFormat(int $glyphDataFormat): void
    {
        $this->glyphDataFormat = $glyphDataFormat;
    }
}
