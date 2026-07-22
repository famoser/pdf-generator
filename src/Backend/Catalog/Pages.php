<?php

namespace Famoser\PdfGenerator\Backend\Catalog;

use Famoser\PdfGenerator\Backend\Catalog\Base\BaseStructure;
use Famoser\PdfGenerator\Backend\CatalogVisitor;
use Famoser\PdfGenerator\Backend\File\Object\Base\BaseObject;

readonly class Pages extends BaseStructure
{
    /**
     * @param Page[] $pages
     */
    public function __construct(private array $pages)
    {
        // no optional objectValues
        parent::__construct();
    }

    public function accept(CatalogVisitor $visitor): BaseObject
    {
        return $visitor->visitPages($this);
    }

    /**
     * @return Page[]
     */
    public function getPages(): array
    {
        return $this->pages;
    }
}
