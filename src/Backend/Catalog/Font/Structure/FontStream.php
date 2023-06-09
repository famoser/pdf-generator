<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Catalog\Font\Structure;

use PdfGenerator\Backend\Catalog\Base\BaseIdentifiableStructure;
use PdfGenerator\Backend\CatalogVisitor;
use PdfGenerator\Backend\File\Object\StreamObject;

class FontStream extends BaseIdentifiableStructure
{
    public const SUBTYPE_OPEN_TYPE = 'OpenType';

    /**
     * should be set to OpenType.
     */
    private string $subtype = self::SUBTYPE_OPEN_TYPE;

    private string $fontData;

    public function getSubtype(): string
    {
        return $this->subtype;
    }

    public function setSubtype(string $subtype): void
    {
        $this->subtype = $subtype;
    }

    public function getFontData(): string
    {
        return $this->fontData;
    }

    public function setFontData(string $fontData): void
    {
        $this->fontData = $fontData;
    }

    public function accept(CatalogVisitor $visitor): StreamObject
    {
        return $visitor->visitFontStream($this);
    }
}
