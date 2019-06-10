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

use PdfGenerator\Backend\Content\Base\BaseContent;
use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\Structure\Base\PageAwareStructure;
use PdfGenerator\Backend\StructureVisitor;

class Contents extends PageAwareStructure
{
    /**
     * @var BaseContent[]
     */
    private $content;

    /**
     * Contents constructor.
     *
     * @param BaseContent[] $content
     */
    public function __construct(array $content)
    {
        $this->content = $content;
    }

    /**
     * @param StructureVisitor $visitor
     * @param Page $page
     *
     * @return BaseObject[]
     */
    public function accept(StructureVisitor $visitor, Page $page)
    {
        return $visitor->visitContents($this, $page);
    }

    /**
     * @return BaseContent[]
     */
    public function getContent(): array
    {
        return $this->content;
    }
}
