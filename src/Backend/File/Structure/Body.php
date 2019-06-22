<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\File\Structure;

use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\File\Structure\Base\BaseStructure;
use PdfGenerator\Backend\File\StructureVisitor;

class Body extends BaseStructure
{
    /**
     * @var BaseObject[]
     */
    private $entries = [];

    /**
     * @param BaseObject $baseObject
     */
    public function addObject(BaseObject $baseObject)
    {
        $this->entries[] = $baseObject;
    }

    /**
     * @return BaseObject[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    /**
     * @param StructureVisitor $visitor
     *
     * @return string
     */
    public function accept(StructureVisitor $visitor): string
    {
        return $visitor->visitBody($this);
    }
}
