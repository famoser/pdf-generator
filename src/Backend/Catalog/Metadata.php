<?php

namespace Famoser\PdfGenerator\Backend\Catalog;

use Famoser\PdfGenerator\Backend\Catalog\Base\BaseStructure;
use Famoser\PdfGenerator\Backend\CatalogVisitor;
use Famoser\PdfGenerator\Backend\File\Object\Base\BaseObject;

readonly class Metadata extends BaseStructure
{
    public function __construct(private string $xml)
    {
        // no optional objectValues
        parent::__construct();
    }

    public function getXml(): string
    {
        return $this->xml;
    }

    public function accept(CatalogVisitor $visitor): BaseObject
    {
        return $visitor->visitMetadata($this);
    }
}
