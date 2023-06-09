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
use PdfGenerator\Backend\Catalog\Font\Structure\CMap;
use PdfGenerator\Backend\CatalogVisitor;
use PdfGenerator\Backend\File\Object\DictionaryObject;

class Type0 extends Font
{
    private string $baseFont;

    private CMap $encoding;

    private Font\Structure\CIDFont $descendantFont;

    private CMap $toUnicode;

    public function getBaseFont(): string
    {
        return $this->baseFont;
    }

    public function setBaseFont(string $baseFont): void
    {
        $this->baseFont = $baseFont;
    }

    public function getEncoding(): CMap
    {
        return $this->encoding;
    }

    public function setEncoding(CMap $encoding): void
    {
        $this->encoding = $encoding;
    }

    public function getDescendantFont(): Structure\CIDFont
    {
        return $this->descendantFont;
    }

    public function setDescendantFont(Structure\CIDFont $descendantFont): void
    {
        $this->descendantFont = $descendantFont;
    }

    public function getToUnicode(): CMap
    {
        return $this->toUnicode;
    }

    public function setToUnicode(CMap $toUnicode): void
    {
        $this->toUnicode = $toUnicode;
    }

    public function accept(CatalogVisitor $visitor): DictionaryObject
    {
        return $visitor->visitType0Font($this);
    }

    public function encode(string $value): string
    {
        return mb_convert_encoding($value, 'UTF-8');
    }
}
