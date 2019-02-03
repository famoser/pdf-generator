<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure;

use PdfGenerator\Backend\File\File;
use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\Structure\Base\BaseStructure;
use PdfGenerator\Backend\StructureVisitor;

class Catalog extends BaseStructure
{
    /**
     * @var Pages
     */
    private $pages;

    /**
     * Catalog constructor.
     */
    public function __construct()
    {
        $this->pages = new Pages();
    }

    /**
     * @param StructureVisitor $visitor
     * @param File $file
     *
     * @return \PdfGenerator\Backend\File\Object\Base\BaseObject
     */
    public function accept(StructureVisitor $visitor, File $file): BaseObject
    {
        return $visitor->visitCatalog($this, $file);
    }

    /**
     * @return Pages
     */
    public function getPages(): Pages
    {
        return $this->pages;
    }
}
