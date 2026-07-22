<?php

namespace Famoser\PdfGenerator\Backend\Catalog;

use Famoser\PdfGenerator\Backend\Catalog\Base\BaseStructure;
use Famoser\PdfGenerator\Backend\CatalogVisitor;
use Famoser\PdfGenerator\Backend\File\Object\Base\BaseObject;

readonly class Resources extends BaseStructure
{
    /**
     * @param Font[]  $fonts
     * @param Image[] $images
     */
    public function __construct(private array $fonts, private array $images)
    {
        // no optional objectValues which easy to define, hence no support needed
        parent::__construct();
    }

    /**
     * @return Font[]
     */
    public function getFonts(): array
    {
        return $this->fonts;
    }

    /**
     * @return Image[]
     */
    public function getImages(): array
    {
        return $this->images;
    }

    public function accept(CatalogVisitor $visitor): BaseObject
    {
        return $visitor->visitResources($this);
    }
}
