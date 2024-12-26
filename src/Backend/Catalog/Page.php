<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\Catalog;

use Famoser\PdfGenerator\Backend\Catalog\Base\BaseStructure;
use Famoser\PdfGenerator\Backend\CatalogVisitor;
use Famoser\PdfGenerator\Backend\File\Object\Base\BaseObject;

readonly class Page extends BaseStructure
{
    /**
     * @param int[] $mediaBox
     */
    public function __construct(private array $mediaBox, private Resources $resources, private Contents $contents)
    {
    }

    public function accept(CatalogVisitor $visitor): BaseObject
    {
        return $visitor->visitPage($this);
    }

    /**
     * @return int[]
     */
    public function getMediaBox(): array
    {
        return $this->mediaBox;
    }

    public function getContents(): Contents
    {
        return $this->contents;
    }

    public function getResources(): Resources
    {
        return $this->resources;
    }
}
