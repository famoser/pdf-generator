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

use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\Structure\Base\BaseStructure;
use PdfGenerator\Backend\StructureVisitor;

class Content extends BaseStructure
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var int
     */
    private $contentType;

    public const CONTENT_TYPE_TEXT = 1;
    public const CONTENT_TYPE_IMAGE = 2;
    public const CONTENT_TYPE_FONT = 3;

    /**
     * Content constructor.
     *
     * @param string $content
     * @param int $contentType
     */
    public function __construct(string $content, int $contentType)
    {
        $this->content = $content;
        $this->contentType = $contentType;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param StructureVisitor $visitor
     *
     * @return BaseObject|BaseObject[]
     */
    public function accept(StructureVisitor $visitor)
    {
        return $visitor->visitContent($this);
    }
}
