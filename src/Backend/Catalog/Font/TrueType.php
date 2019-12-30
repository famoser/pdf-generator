<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Catalog\Font;

use PdfGenerator\Backend\Catalog\Font;
use PdfGenerator\Backend\CatalogVisitor;
use PdfGenerator\Backend\File\Object\Base\BaseObject;

class TrueType extends Font
{
    /**
     * @var string
     */
    private $baseFont;

    /**
     * @var int
     */
    private $firstChar;

    /**
     * @var int
     */
    private $lastChar;

    /**
     * @var int[]
     */
    private $widths;

    /**
     * @var Font\Structure\FontDescriptor
     */
    private $fontDescriptor;

    public function getBaseFont(): string
    {
        return $this->baseFont;
    }

    public function setBaseFont(string $baseFont): void
    {
        $this->baseFont = $baseFont;
    }

    public function getFirstChar(): int
    {
        return $this->firstChar;
    }

    public function setFirstChar(int $firstChar): void
    {
        $this->firstChar = $firstChar;
    }

    public function getLastChar(): int
    {
        return $this->lastChar;
    }

    public function setLastChar(int $lastChar): void
    {
        $this->lastChar = $lastChar;
    }

    /**
     * @return int[]
     */
    public function getWidths(): array
    {
        return $this->widths;
    }

    /**
     * @param int[] $widths
     */
    public function setWidths(array $widths): void
    {
        $this->widths = $widths;
    }

    public function getFontDescriptor(): Structure\FontDescriptor
    {
        return $this->fontDescriptor;
    }

    public function setFontDescriptor(Structure\FontDescriptor $fontDescriptor): void
    {
        $this->fontDescriptor = $fontDescriptor;
    }

    /**
     * @return BaseObject|BaseObject[]
     */
    public function accept(CatalogVisitor $visitor)
    {
        return $visitor->visitTrueTypeFont($this);
    }
}
