<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\File\Structure;

use Famoser\PdfGenerator\Backend\File\Object\Base\BaseObject;
use Famoser\PdfGenerator\Backend\File\Structure\Base\BaseStructure;
use Famoser\PdfGenerator\Backend\File\StructureVisitor;

class Body extends BaseStructure
{
    /**
     * @var BaseObject[]
     */
    private array $entries = [];

    private BaseObject $infoObject;

    public function addObject(BaseObject $baseObject): void
    {
        $this->entries[] = $baseObject;
    }

    public function addInfoObject(BaseObject $baseObject): void
    {
        $this->addObject($baseObject);
        $this->infoObject = $baseObject;
    }

    /**
     * @return BaseObject[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    public function getRootEntry(): BaseObject
    {
        return $this->entries[0];
    }

    public function getInfoObject(): BaseObject
    {
        return $this->infoObject;
    }

    public function accept(StructureVisitor $visitor): string
    {
        return $visitor->visitBody($this);
    }
}
