<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Catalog\Font;

use PdfGenerator\Backend\Catalog\Font;
use PdfGenerator\Backend\Catalog\Font\Structure\CMap;
use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\StructureVisitor;

class Type0 extends Font
{
    /**
     * @var string
     */
    private $baseFont;

    /**
     * @var CMap
     */
    private $encoding;

    /**
     * @var Font\Structure\CIDFont
     */
    private $descendantFont;

    /**
     * @var CMap
     */
    private $toUnicode;

    /**
     * @return string
     */
    public function getBaseFont(): string
    {
        return $this->baseFont;
    }

    /**
     * @param string $baseFont
     */
    public function setBaseFont(string $baseFont): void
    {
        $this->baseFont = $baseFont;
    }

    /**
     * @return CMap
     */
    public function getEncoding(): CMap
    {
        return $this->encoding;
    }

    /**
     * @param CMap $encoding
     */
    public function setEncoding(CMap $encoding): void
    {
        $this->encoding = $encoding;
    }

    /**
     * @return Structure\CIDFont
     */
    public function getDescendantFont(): Structure\CIDFont
    {
        return $this->descendantFont;
    }

    /**
     * @param Structure\CIDFont $descendantFont
     */
    public function setDescendantFont(Structure\CIDFont $descendantFont): void
    {
        $this->descendantFont = $descendantFont;
    }

    /**
     * @return CMap
     */
    public function getToUnicode(): CMap
    {
        return $this->toUnicode;
    }

    /**
     * @param CMap $toUnicode
     */
    public function setToUnicode(CMap $toUnicode): void
    {
        $this->toUnicode = $toUnicode;
    }

    /**
     * @param StructureVisitor $visitor
     *
     * @return BaseObject|BaseObject[]
     */
    public function accept(StructureVisitor $visitor)
    {
        return $visitor->visitType0Font($this, $file);
    }
}
