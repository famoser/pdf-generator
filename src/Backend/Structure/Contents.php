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
use PdfGenerator\Backend\File\File;
use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\Structure\Base\BaseStructure;
use PdfGenerator\Backend\StructureVisitor;

class Contents extends BaseStructure
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
     * @param File $file
     *
     * @return BaseObject[]
     */
    public function accept(StructureVisitor $visitor, File $file): array
    {
        return $visitor->visitContents($this, $file);
    }

    /**
     * @return BaseContent[]
     */
    public function getContent(): array
    {
        return $this->content;
    }
}
