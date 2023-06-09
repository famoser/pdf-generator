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

class Contents extends BaseStructure
{
    /**
     * Contents constructor.
     *
     * @param Content[] $content
     */
    public function __construct(private readonly array $content)
    {
    }

    /**
     * @return BaseObject[]
     */
    public function accept(CatalogVisitor $visitor): array
    {
        return $visitor->visitContents($this);
    }

    /**
     * @return Content[]
     */
    public function getContent(): array
    {
        return $this->content;
    }
}
