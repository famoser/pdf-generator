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
use PdfGenerator\Backend\Catalog\Base\IdentifiableStructureTrait;
use PdfGenerator\Backend\CatalogVisitor;
use PdfGenerator\Backend\File\Object\Base\BaseObject;

class FontDescriptor extends BaseStructure
{
    use IdentifiableStructureTrait;

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

    /**
     * @var FontStream|null
     */
    private $fontFile3;

    /**
     * @return string
     */
    public function getFontName(): string
    {
        return $this->fontName;
    }

    /**
     * @param string $fontName
     */
    public function setFontName(string $fontName): void
    {
        $this->fontName = $fontName;
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
     * @return int[]
     */
    public function getFontBBox(): array
    {
        return $this->fontBBox;
    }

    /**
     * @param int[] $fontBBox
     */
    public function setFontBBox(array $fontBBox): void
    {
        $this->fontBBox = $fontBBox;
    }

    /**
     * @return int
     */
    public function getItalicAngle(): int
    {
        return $this->italicAngle;
    }

    /**
     * @param int $italicAngle
     */
    public function setItalicAngle(int $italicAngle): void
    {
        $this->italicAngle = $italicAngle;
    }

    /**
     * @return int
     */
    public function getAscent(): int
    {
        return $this->ascent;
    }

    /**
     * @param int $ascent
     */
    public function setAscent(int $ascent): void
    {
        $this->ascent = $ascent;
    }

    /**
     * @return int
     */
    public function getDecent(): int
    {
        return $this->decent;
    }

    /**
     * @param int $decent
     */
    public function setDecent(int $decent): void
    {
        $this->decent = $decent;
    }

    /**
     * @return int
     */
    public function getCapHeight(): int
    {
        return $this->capHeight;
    }

    /**
     * @param int $capHeight
     */
    public function setCapHeight(int $capHeight): void
    {
        $this->capHeight = $capHeight;
    }

    /**
     * @return int
     */
    public function getStemV(): int
    {
        return $this->stemV;
    }

    /**
     * @param int $stemV
     */
    public function setStemV(int $stemV): void
    {
        $this->stemV = $stemV;
    }

    /**
     * @return FontStream|null
     */
    public function getFontFile3(): ?FontStream
    {
        return $this->fontFile3;
    }

    /**
     * @param FontStream|null $fontFile3
     */
    public function setFontFile3(?FontStream $fontFile3): void
    {
        $this->fontFile3 = $fontFile3;
    }

    /**
     * @param CatalogVisitor $visitor
     *
     * @return BaseObject|BaseObject[]
     */
    public function accept(CatalogVisitor $visitor)
    {
        return $visitor->visitFontDescriptor($this);
    }
}
