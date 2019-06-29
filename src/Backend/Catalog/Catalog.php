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
     * @var Pages
     */
    private $pages;

    /**
     * Catalog constructor.
     *
     * @param Pages $pages
     */
    public function __construct(Pages $pages)
    {
        $this->pages = $pages;
    }

    /**
     * @param CatalogVisitor $visitor
     *
     * @return BaseObject
     */
    public function accept(CatalogVisitor $visitor)
    {
        return $visitor->visitCatalog($this);
    }

    /**
     * @return Pages
     */
    public function getPages(): Pages
    {
        return $this->pages;
    }

    /**
     * @return string
     */
    public function render()
    {
        $file = new File();
        $structureVisitor = new CatalogVisitor($file);

        $structureVisitor->visitCatalog($this);

        return $file->render();
    }

    /**
     * @return string
     */
    public function save()
    {
        return $this->render();
    }
}
