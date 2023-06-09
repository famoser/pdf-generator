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

class Content extends BaseStructure
{
    /**
     * Content constructor.
     */
    public function __construct(private readonly string $content)
    {
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return BaseObject|BaseObject[]
     */
    public function accept(CatalogVisitor $visitor): BaseObject|array
    {
        return $visitor->visitContent($this);
    }
}
