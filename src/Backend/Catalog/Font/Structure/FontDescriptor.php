<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Catalog\Font\Structure;

use PdfGenerator\Backend\Catalog\Base\BaseStructure;
use PdfGenerator\Backend\CatalogVisitor;
use PdfGenerator\Backend\File\Object\DictionaryObject;

readonly class FontDescriptor extends BaseStructure
{
    /**
     * all characters have same with.
     */
    final public const FLAG_FIXED_PITCH = 1;

    /**
     * contains serif (used in books).
     */
    final public const FLAG_SERIF = 2;

    /**
     * contains characters outside the Adobe standard latin range.
     */
    final public const FLAG_SYMBOLIC = 4;

    /**
     * glyphs resemble cursive handwriting.
     */
    final public const FLAG_SCRIPT = 8;

    /**
     * no characters outside the Adobe standard latin range.
     */
    final public const FLAG_NON_SYMBOLIC = 16;

    /**
     * glyphs have slanted vertical strokes.
     */
    final public const FLAG_ITALIC = 32;

    /**
     * no lowercase letters contained.
     */
    final public const FLAG_ALL_CAP = 131072; // 2^17

    /**
     * lowercase glyphs are like the uppercase glyphs but smaller.
     */
    final public const FLAG_SMALL_CAP = 262144; // 2^18

    /**
     * will advise readers to print extra pixels for lower text sizes.
     */
    final public const FLAG_FORCE_BOLD = 524288; // 2^19

    /**
     * @param string $fontName    same value than from the referencing entry
     * @param int    $flags       characteristics of the font
     * @param int[]  $fontBBox    the max bounding box of all characters
     * @param int    $italicAngle angle of the font (negative for slope to the right)
     * @param int    $ascent      max height above baseline
     * @param int    $descent     max depth below baseline
     * @param int    $capHeight   height of cap characters
     * @param int    $stemV       thickness of dominant stems (de: stängel) of characters
     */
    public function __construct(private string $fontName, private int $flags, private array $fontBBox, private int $italicAngle, private int $ascent, private int $descent, private int $capHeight, private int $stemV, private FontStream $fontFile3)
    {
    }

    public function getFontName(): string
    {
        return $this->fontName;
    }

    public function getFlags(): int
    {
        return $this->flags;
    }

    /**
     * @return int[]
     */
    public function getFontBBox(): array
    {
        return $this->fontBBox;
    }

    public function getItalicAngle(): int
    {
        return $this->italicAngle;
    }

    public function getAscent(): int
    {
        return $this->ascent;
    }

    public function getDescent(): int
    {
        return $this->descent;
    }

    public function getCapHeight(): int
    {
        return $this->capHeight;
    }

    public function getStemV(): int
    {
        return $this->stemV;
    }

    public function getFontFile3(): ?FontStream
    {
        return $this->fontFile3;
    }

    public function accept(CatalogVisitor $visitor): DictionaryObject
    {
        return $visitor->visitFontDescriptor($this);
    }
}
