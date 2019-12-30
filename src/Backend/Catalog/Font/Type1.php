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
use PdfGenerator\Backend\CatalogVisitor;
use PdfGenerator\Backend\File\Object\Base\BaseObject;

class Type1 extends Font
{
    const BASE_FONT_TIMES__ROMAN = 'Times-Roman';
    const BASE_FONT_HELVETICA = 'Helvetica';
    const BASE_FONT_COURIER = 'Courier';
    const BASE_FONT_SYMBOL = 'Symbol';
    const BASE_FONT_TIMES__BOLD = 'Times-Bold';
    const BASE_FONT_HELVETICA__BOLD = 'Helvetica-Bold';
    const BASE_FONT_COURIER__BOLD = 'Courier-Bold';
    const BASE_FONT_ZAPFDINGBATS = 'ZapfDingbats';
    const BASE_FONT_TIMES__ITALIC = 'Times-Italic';
    const BASE_FONT_HELVETICA__OBLIQUE = 'Helvetica-Oblique';
    const BASE_FONT_COURIER__OBLIQUE = 'Courier-Oblique';
    const BASE_FONT_TIMES__BOLDITALIC = 'Times-BoldItalic';
    const BASE_FONT_HELVETICA__BOLDOBLIQUE = 'Helvetica-BoldOblique';
    const BASE_FONT_COURIER__BOLDOBLIQUE = 'Courier-BoldOblique';

    /**
     * @var string
     */
    private $baseFont;

    /**
     * @var string
     */
    private $encoding;

    /**
     * Type1 constructor.
     */
    public function __construct(string $identifier, string $baseFont, string $encoding)
    {
        parent::__construct($identifier);

        $this->baseFont = $baseFont;
        $this->encoding = $encoding;
    }

    public function getBaseFont(): string
    {
        return $this->baseFont;
    }

    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * @return BaseObject
     */
    public function accept(CatalogVisitor $visitor)
    {
        return $visitor->visitType1Font($this);
    }
}
