<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure;

use PdfGenerator\Backend\File;
use PdfGenerator\Backend\Object\Base\BaseObject;
use PdfGenerator\IR\Structure\Base\BaseStructure;
use PdfGenerator\IR\StructureVisitor;

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
     * @return \PdfGenerator\Backend\Object\Base\BaseObject
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
