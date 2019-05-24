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
use PdfGenerator\Backend\StructureVisitor;

class SimpleFont extends Font
{
    const SUBTYPE_TYPE1 = 'Type1';
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
     * @param StructureVisitor $visitor
     * @param File $file
     *
     * @return BaseObject
     */
    public function accept(StructureVisitor $visitor, File $file): BaseObject
    {
        return $visitor->visitSimpleFont($this, $file);
    }
}
