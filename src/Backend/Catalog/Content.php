<?php

namespace Famoser\PdfGenerator\Backend\Catalog;

use Famoser\PdfGenerator\Backend\Catalog\Base\BaseStructure;
use Famoser\PdfGenerator\Backend\CatalogVisitor;
use Famoser\PdfGenerator\Backend\File\Object\StreamObject;

readonly class Content extends BaseStructure
{
    public function __construct(private string $content)
    {
        // encoding/decoding not supported at the moment -> to make file easily machine-readable, should not use compression in streams
        // no other relevant objectValues
        parent::__construct();
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function accept(CatalogVisitor $visitor): StreamObject
    {
        return $visitor->visitContent($this);
    }
}
