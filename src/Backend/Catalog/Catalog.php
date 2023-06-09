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
use PdfGenerator\Backend\File\File;
use PdfGenerator\Backend\File\Object\Base\BaseObject;

class Catalog extends BaseStructure
{
    /**
     * Catalog constructor.
     */
    public function __construct(private Pages $pages)
    {
    }

    public function accept(CatalogVisitor $visitor): BaseObject
    {
        return $visitor->visitCatalog($this);
    }

    public function getPages(): Pages
    {
        return $this->pages;
    }

    public function render(): string
    {
        $file = new File();
        $structureVisitor = new CatalogVisitor($file);

        $structureVisitor->visitCatalog($this);

        return $file->render();
    }

    public function save(): string
    {
        return $this->render();
    }
}
