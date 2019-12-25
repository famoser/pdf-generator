<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Catalog;

use PdfGenerator\Backend\Catalog\Base\BaseStructure;
use PdfGenerator\Backend\CatalogVisitor;
use PdfGenerator\Backend\File\Object\Base\BaseObject;

class Resources extends BaseStructure
{
    /**
     * @var Font[]
     */
    private $fonts = [];

    /**
     * @var Image[]
     */
    private $images = [];

    public function addFont(Font $font)
    {
        $this->fonts[] = $font;
    }

    public function addImage(Image $image)
    {
        $this->images[] = $image;
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

    /**
     * @return BaseObject
     */
    public function accept(CatalogVisitor $visitor)
    {
        return $visitor->visitResources($this);
    }
}
