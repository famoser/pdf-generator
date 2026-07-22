<?php

namespace Famoser\PdfGenerator\Backend\Catalog;

use Famoser\PdfGenerator\Backend\Catalog\Base\BaseIdentifiableStructure;
use Famoser\PdfGenerator\Backend\CatalogVisitor;
use Famoser\PdfGenerator\Backend\File\Object\Base\BaseObject;

abstract readonly class Font extends BaseIdentifiableStructure
{
    public function __construct(string $identifier)
    {
        parent::__construct($identifier);
    }

    abstract public function encode(string $value): string;

    abstract public function accept(CatalogVisitor $visitor): BaseObject;
}
