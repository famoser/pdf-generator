<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Font\Structure;

use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\Structure\Base\BaseStructure;
use PdfGenerator\Backend\Structure\Base\IdentifiableStructureTrait;
use PdfGenerator\Backend\StructureVisitor;

class FontStream extends BaseStructure
{
    use IdentifiableStructureTrait;

    public const SUBTYPE_OPEN_TYPE = 'OpenType';

    /**
     * should be set to OpenType.
     *
     * @var string
     */
    private $subtype = self::SUBTYPE_OPEN_TYPE;

    /**
     * @var string
     */
    private $fontData;

    /**
     * @return string
     */
    public function getSubtype(): string
    {
        return $this->subtype;
    }

    /**
     * @param string $subtype
     */
    public function setSubtype(string $subtype): void
    {
        $this->subtype = $subtype;
    }

    /**
     * @return string
     */
    public function getFontData(): string
    {
        return $this->fontData;
    }

    /**
     * @param string $fontData
     */
    public function setFontData(string $fontData): void
    {
        $this->fontData = $fontData;
    }

    /**
     * @param StructureVisitor $visitor
     *
     * @return BaseObject|BaseObject[]
     */
    public function accept(StructureVisitor $visitor)
    {
        return $visitor->visitFontStream($this, $file);
    }
}
