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

    const ENCODING_WIN_ANSI_ENCODING = 'WinAnsiEncoding';

    /**
     * @var string
     */
    private $baseFont;

    /**
     * Type1 constructor.
     */
    public function __construct(string $identifier, string $baseFont)
    {
        parent::__construct($identifier);

        $this->baseFont = $baseFont;
    }

    public function getBaseFont(): string
    {
        return $this->baseFont;
    }

    public function getEncoding(): string
    {
        return self::ENCODING_WIN_ANSI_ENCODING;
    }

    /**
     * @return BaseObject
     */
    public function accept(CatalogVisitor $visitor)
    {
        return $visitor->visitType1Font($this);
    }

    /**
     * sets the encoding used by the font.
     */
    public function encode(string $escaped): string
    {
        /* windows-1252 is equivalent WinAnsiEncoding according to comment https://www.php.net/manual/de/haru.builtin.encodings.php.*/
        return mb_convert_encoding($escaped, 'Windows-1252', 'UTF-8');
    }
}
