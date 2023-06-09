<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Backend\File\Table;

use PdfGenerator\Font\Backend\File\Table\Base\BaseTable;
use PdfGenerator\Font\Backend\File\TableVisitor;
use PdfGenerator\Font\Backend\File\Traits\BoundingBoxTrait;

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
class HeadTable extends BaseTable
{
    use BoundingBoxTrait;
    /**
     * baseline for font at y=0
     * must be set for variable fonts.
     */
    public const FLAG_BASELINE_AT_0 = 1;

    /**
     * left sidebearing point at x=0.
     */
    public const FLAG_LEFTBEARING_POINT_AT_0 = 2;

    /**
     * instructions may depend on font size.
     */
    public const FLAG_INSTRUCTION_DEPENDENT_ON_POINT_SIZE = 4;

    /**
     * must use integer values for scaler math (else could use fractional).
     */
    public const FLAG_FORCE_INTEGER_PPEM_VALUES = 8;

    /**
     * instructions may alter the advance width
     * must be set for variable fonts.
     */
    public const FLAG_INSTRUCTIONS_ALTER_WIDTH = 16;

    /**
     * the binary layout of the file has changed but original functionality was retained.
     */
    public const FLAG_TRANSFORMED = 2048;

    /**
     * the font has been converted.
     */
    public const FLAG_CONVERTED = 4056;

    /**
     * optimized for clear type.
     */
    public const FLAG_CLEAR_TYPE_OPTIMIZED = 8092;

    /**
     * font is a last resort font
     * as a result, the cmap table behaves differently.
     */
    public const FLAG_LAST_RESORT_FONT = 14;

    /**
     * the styles of the font.
     */
    public const MAC_STYLE_BOLD = 1;
    public const MAC_STYLE_ITALIC = 2;
    public const MAC_STYLE_UNDERLINE = 4;
    public const MAC_STYLE_OUTLINE = 8;
    public const MAC_STYLE_SHADOW = 16;
    public const MAC_STYLE_CONDENSED = 32;
    public const MAC_STYLE_EXTENDED = 64;

    /**
     * in which way the font should be read
     * for "normal" roman fonts the value is 2 because of punctuation/neutral characters.
     */
    public const FONT_DIRECTION_RIGHT_TO_LEFT_WITH_NEUTRALS = -2;
    public const FONT_DIRECTION_RIGHT_TO_LEFT = -1;
    public const FONT_DIRECTION_MIXED = 0;
    public const FONT_DIRECTION_LEFT_TO_RIGHT = 1;
    public const FONT_DIRECTION_LEFT_TO_RIGHT_WITH_NEUTRALS = 2;

    /**
     * major version of this font.
     *
     * @ttf-type uint16
     */
    private int $majorVersion;

    /**
     * minor version of this font.
     *
     * @ttf-type uint16
     */
    private int $minorVersion;

    /**
     * font revision set by producer
     * ignored by microsoft, instead uses the version from the name table.
     *
     * @ttf-type fixed
     */
    private float $fontRevision;

    /**
     * check sum adjusted.
     *
     * @ttf-type uint32
     */
    private int $checkSumAdjustment;

    /**
     * magic number set to 0x5F0F3CF5.
     *
     * @ttf-type uint32
     */
    private int $magicNumber;

    /**
     * flags of this font.
     *
     * @ttf-type uint16
     */
    private int $flags;

    /**
     * how many units used per em
     * 16-16384 is allowed; recommended are powers of 2.
     *
     * @ttf-type uint16
     */
    private int $unitsPerEm;

    /**
     * when the font was created.
     *
     * @ttf-type int
     */
    private int $created;

    /**
     * when the font was last modified.
     *
     * @ttf-type int
     */
    private int $modified;

    /**
     * the style of the font.
     *
     * @ttf-type uint16
     */
    private int $macStyle;

    /**
     * smallest readable size in pixels.
     *
     * @ttf-type uint16
     */
    private int $lowestRecPPEM;

    /**
     * in which way the characters should be read.
     *
     * @ttf-type int16
     */
    private int $fontDirectionHints;

    /**
     * which format the Loca table is in
     * 0 for short offset16[]
     * 1 for long offset32[].
     *
     * @ttf-type int16
     */
    private int $indexToLocFormat;

    /**
     * which format the glyf table is in
     * 0 for current format.
     *
     * @ttf-type int16
     */
    private int $glyphDataFormat;

    public function getMajorVersion(): int
    {
        return $this->majorVersion;
    }

    public function setMajorVersion(int $majorVersion): void
    {
        $this->majorVersion = $majorVersion;
    }

    public function getMinorVersion(): int
    {
        return $this->minorVersion;
    }

    public function setMinorVersion(int $minorVersion): void
    {
        $this->minorVersion = $minorVersion;
    }

    public function getFontRevision(): float
    {
        return $this->fontRevision;
    }

    public function setFontRevision(float $fontRevision): void
    {
        $this->fontRevision = $fontRevision;
    }

    public function getCheckSumAdjustment(): int
    {
        return $this->checkSumAdjustment;
    }

    public function setCheckSumAdjustment(int $checkSumAdjustment): void
    {
        $this->checkSumAdjustment = $checkSumAdjustment;
    }

    public function getMagicNumber(): int
    {
        return $this->magicNumber;
    }

    public function setMagicNumber(int $magicNumber): void
    {
        $this->magicNumber = $magicNumber;
    }

    public function getFlags(): int
    {
        return $this->flags;
    }

    public function setFlags(int $flags): void
    {
        $this->flags = $flags;
    }

    public function getUnitsPerEm(): int
    {
        return $this->unitsPerEm;
    }

    public function setUnitsPerEm(int $unitsPerEm): void
    {
        $this->unitsPerEm = $unitsPerEm;
    }

    public function getCreated(): int
    {
        return $this->created;
    }

    public function setCreated(int $created): void
    {
        $this->created = $created;
    }

    public function getModified(): int
    {
        return $this->modified;
    }

    public function setModified(int $modified): void
    {
        $this->modified = $modified;
    }

    public function getMacStyle(): int
    {
        return $this->macStyle;
    }

    public function setMacStyle(int $macStyle): void
    {
        $this->macStyle = $macStyle;
    }

    public function getLowestRecPPEM(): int
    {
        return $this->lowestRecPPEM;
    }

    public function setLowestRecPPEM(int $lowestRecPPEM): void
    {
        $this->lowestRecPPEM = $lowestRecPPEM;
    }

    public function getFontDirectionHints(): int
    {
        return $this->fontDirectionHints;
    }

    public function setFontDirectionHints(int $fontDirectionHints): void
    {
        $this->fontDirectionHints = $fontDirectionHints;
    }

    public function getIndexToLocFormat(): int
    {
        return $this->indexToLocFormat;
    }

    public function setIndexToLocFormat(int $indexToLocFormat): void
    {
        $this->indexToLocFormat = $indexToLocFormat;
    }

    public function getGlyphDataFormat(): int
    {
        return $this->glyphDataFormat;
    }

    public function setGlyphDataFormat(int $glyphDataFormat): void
    {
        $this->glyphDataFormat = $glyphDataFormat;
    }

    public function accept(TableVisitor $visitor): string
    {
        return $visitor->visitHeadTable($this);
    }
}
