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

use Pdf\Backend\File;
use Pdf\Backend\Object\Base\BaseObject;
use Pdf\IR\Structure\Base\BaseStructure;
use Pdf\IR\StructureVisitor;

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
