<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Font;

use PdfGenerator\Backend\File\File;
use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\Structure\Font;
use PdfGenerator\Backend\Structure\Font\Composite\CMap;
use PdfGenerator\Backend\StructureVisitor;

class CompositeFont extends Font
{
    /**
     * @var CMap
     */
    private $encoding;

    /**
     * @var Font\Composite\CIDFont
     */
    private $descendantFont;

    /**
     * @var CMap
     */
    private $toUnicode;

    /**
     * @param StructureVisitor $visitor
     * @param File $file
     *
     * @return BaseObject|BaseObject[]
     */
    public function accept(StructureVisitor $visitor, File $file)
    {
        return $visitor->visitCompositeFont($this, $file);
    }
}
