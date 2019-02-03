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

class Pages extends BaseStructure
{
    /**
     * @var Page[]
     */
    private $kids = [];

    /**
     * @param Page $page
     */
    public function addPage(Page $page)
    {
        $this->kids[] = $page;
    }

    /**
     * @param StructureVisitor $visitor
     * @param File $file
     *
     * @return BaseObject
     */
    public function accept(StructureVisitor $visitor, File $file): BaseObject
    {
        return $visitor->visitPages($this, $file);
    }

    /**
     * @return Page[]
     */
    public function getKids(): array
    {
        return $this->kids;
    }
}
