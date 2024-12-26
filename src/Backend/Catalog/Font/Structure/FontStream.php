<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\Catalog\Font\Structure;

use Famoser\PdfGenerator\Backend\Catalog\Base\BaseStructure;
use Famoser\PdfGenerator\Backend\CatalogVisitor;
use Famoser\PdfGenerator\Backend\File\Object\StreamObject;

readonly class FontStream extends BaseStructure
{
    final public const SUBTYPE_OPEN_TYPE = 'OpenType';

    public function __construct(private string $subtype, private string $fontData)
    {
    }

    public function getSubtype(): string
    {
        return $this->subtype;
    }

    public function getFontData(): string
    {
        return $this->fontData;
    }

    public function accept(CatalogVisitor $visitor): StreamObject
    {
        return $visitor->visitFontStream($this);
    }
}
