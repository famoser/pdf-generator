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
    private string $content;

    private int $contentType;

    public const CONTENT_TYPE_TEXT = 1;
    public const CONTENT_TYPE_IMAGE = 2;
    public const CONTENT_TYPE_FONT = 3;

    /**
     * Content constructor.
     */
    public function __construct(string $content, int $contentType)
    {
        $this->content = $content;
        $this->contentType = $contentType;
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
