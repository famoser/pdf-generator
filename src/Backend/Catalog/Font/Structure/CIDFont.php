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

class CIDFont extends BaseStructure
{
    public const SUBTYPE_CID_FONT_TYPE_2 = 'CIDFontType2';

    private string $subType = self::SUBTYPE_CID_FONT_TYPE_2;

    /**
     * determined by looking at the TTF 'name' table (9.6.3)
     * if subset, then prefix with 6 uppercase letters unique for that specific subset followed by a + sign.
     */
    private string $baseFont;

    private CIDSystemInfo $cIDSystemInfo;

    private FontDescriptor $fontDescriptor;

    /**
     * default width of a character.
     */
    private int $dW = 1000;

    /**
     * width per character.
     * for int[][], the first dimensions defines the character code the widths start at
     * so [32 => [120, 271]] defines that space (code 32) has width 120, the following character width 271.
     *
     * @var int[]|int[][]
     */
    private array $w = [];

    public function getSubType(): string
    {
        return $this->subType;
    }

    public function setSubType(string $subType): void
    {
        $this->subType = $subType;
    }

    public function getBaseFont(): string
    {
        return $this->baseFont;
    }

    public function setBaseFont(string $baseFont): void
    {
        $this->baseFont = $baseFont;
    }

    public function getCIDSystemInfo(): CIDSystemInfo
    {
        return $this->cIDSystemInfo;
    }

    public function setCIDSystemInfo(CIDSystemInfo $cIDSystemInfo): void
    {
        $this->cIDSystemInfo = $cIDSystemInfo;
    }

    public function getFontDescriptor(): FontDescriptor
    {
        return $this->fontDescriptor;
    }

    public function setFontDescriptor(FontDescriptor $fontDescriptor): void
    {
        $this->fontDescriptor = $fontDescriptor;
    }

    public function getDW(): int
    {
        return $this->dW;
    }

    public function setDW(int $dW): void
    {
        $this->dW = $dW;
    }

    /**
     * @return int[]|int[][]
     */
    public function getW(): array
    {
        return $this->w;
    }

    /**
     * @param int[]|int[][] $w
     */
    public function setW(array $w): void
    {
        $this->w = $w;
    }

    public function accept(CatalogVisitor $visitor): DictionaryObject
    {
        return $visitor->visitCIDFont($this);
    }
}
