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

class Page extends BaseStructure
{
    use IdentifiableTrait;

    /**
     * @var Pages
     */
    private $parent;

    /**
     * @var int[]
     */
    private $mediaBox;

    /**
     * @var Contents
     */
    private $contents;

    /**
     * @var Resources
     */
    private $resources;

    /**
     * Page constructor.
     */
    public function __construct(Pages $parent, array $mediaBox, Resources $resources, Contents $contents)
    {
        $this->parent = $parent;
        $this->mediaBox = $mediaBox;
        $this->contents = $contents;
        $this->resources = $resources;
    }

    /**
     * @return BaseObject
     */
    public function accept(CatalogVisitor $visitor)
    {
        return $visitor->visitPage($this);
    }

    public function getParent(): Pages
    {
        return $this->parent;
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
