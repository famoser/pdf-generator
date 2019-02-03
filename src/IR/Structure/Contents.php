<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure;

use PdfGenerator\Backend\File;
use PdfGenerator\Backend\Object\Base\BaseObject;
use PdfGenerator\IR\Content\Base\BaseContent;
use PdfGenerator\IR\Structure\Base\BaseStructure;
use PdfGenerator\IR\StructureVisitor;

class Contents extends BaseStructure
{
    /**
     * @var BaseContent
     */
    private $content;

    /**
     * Contents constructor.
     *
     * @param BaseContent $content
     */
    public function __construct(BaseContent $content)
    {
        $this->content = $content;
    }

    /**
     * @param StructureVisitor $visitor
     * @param File $file
     *
     * @return BaseObject
     */
    public function accept(StructureVisitor $visitor, File $file): BaseObject
    {
        return $visitor->visitContents($this, $file);
    }

    /**
     * @return BaseContent
     */
    public function getContent(): BaseContent
    {
        return $this->content;
    }
}
