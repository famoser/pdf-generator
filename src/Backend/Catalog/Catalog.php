<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\Catalog;

use Famoser\PdfGenerator\Backend\Catalog\Base\BaseStructure;
use Famoser\PdfGenerator\Backend\CatalogVisitor;
use Famoser\PdfGenerator\Backend\File\File;
use Famoser\PdfGenerator\Backend\File\Object\Base\BaseObject;

readonly class Catalog extends BaseStructure
{
    public function __construct(private Pages $pages, private Metadata $metadata)
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

    public function getMetadata(): Metadata
    {
        return $this->metadata;
    }

    public function render(): File
    {
        $file = new File();
        $structureVisitor = new CatalogVisitor($file);

        $structureVisitor->visitCatalog($this);

        return $file;
    }

    public function save(): string
    {
        return $this->render()->render();
    }
}
