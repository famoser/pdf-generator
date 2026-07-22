<?php

namespace Famoser\PdfGenerator\Backend\Catalog\Font\Structure;

use Famoser\PdfGenerator\Backend\Catalog\Base\BaseStructure;
use Famoser\PdfGenerator\Backend\CatalogVisitor;
use Famoser\PdfGenerator\Backend\File\Object\StreamObject;

readonly class FontStream extends BaseStructure
{
    final public const SUBTYPE_OPEN_TYPE = 'TrueType'; // "The value of Subtype shall be TrueType", even if OpenType format

    public function __construct(private string $subtype, private string $fontData)
    {
        parent::__construct();
    }

    public function getSubtype(): string
    {
        return $this->subtype;
    }

    public function getFontData(): string
    {
        return $this->fontData;
    }

    public function accept(CatalogVisitor $visitor): StreamObject
    {
        return $visitor->visitFontStream($this);
    }
}
