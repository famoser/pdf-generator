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
use PdfGenerator\Backend\File\Object\DictionaryObject;

class TrueType extends Type1
{
    private readonly Font\Structure\FontDescriptor $fontDescriptor;

    /**
     * @param int[] $widths
     */
    public function __construct(string $identifier, Font\Structure\FontDescriptor $fontDescriptor, private readonly array $widths)
    {
        parent::__construct($identifier, $fontDescriptor->getFontName());
        $this->fontDescriptor = $fontDescriptor;
    }

    /**
     * @return int[]
     */
    public function getWidths(): array
    {
        return $this->widths;
    }

    public function getFontDescriptor(): Structure\FontDescriptor
    {
        return $this->fontDescriptor;
    }

    public function accept(CatalogVisitor $visitor): DictionaryObject
    {
        return $visitor->visitTrueTypeFont($this);
    }

    public function encode(string $value): string
    {
        return mb_convert_encoding($value, 'Windows-1252', 'UTF-8');
    }
}
