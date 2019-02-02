<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\IR\Structure;

use Pdf\Backend\Object\Base\BaseObject;
use Pdf\Backend\Structure\File;
use Pdf\IR\Structure\Base\BaseStructure;
use Pdf\IR\StructureVisitor;

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
     * @return Pages
     */
    public function getPages(): Pages
    {
        return $this->pages;
    }

    /**
     * @param StructureVisitor $visitor
     * @param File $file
     *
     * @return \Pdf\Backend\Object\Base\BaseObject
     */
    public function accept(StructureVisitor $visitor, File $file): BaseObject
    {
        return $visitor->visitCatalog($this, $file);
    }
}
