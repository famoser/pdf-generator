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
use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Utils\IdentifiableTrait;

class Pages extends BaseStructure
{
    use IdentifiableTrait;

    /**
     * @var Page[]
     */
    private $kids = [];

    public function addPage(Page $page)
    {
        $this->kids[] = $page;
    }

    /**
     * @return BaseObject
     */
    public function accept(CatalogVisitor $visitor)
    {
        return $visitor->visitPages($this);
    }

    /**
     * @return Page[]
     */
    public function getKids(): array
    {
        return $this->kids;
    }
}
