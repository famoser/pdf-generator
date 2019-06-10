<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR;

use PdfGenerator\Backend\Document;
use PdfGenerator\Backend\Structure\Font\Type1;
use PdfGenerator\IR\Structure2\Font\DefaultFont;

class Structure2Visitor
{
    /**
     * @var string[][]
     */
    private static $defaultFontMapping = [
        DefaultFont::FONT_HELVETICA => [
            DefaultFont::STYLE_DEFAULT => Type1::BASE_FONT_HELVETICA,
            DefaultFont::STYLE_OBLIQUE => Type1::BASE_FONT_HELVETICA__OBLIQUE,
            DefaultFont::STYLE_BOLD => Type1::BASE_FONT_HELVETICA__BOLD,
            DefaultFont::STYLE_BOLD_OBLIQUE => Type1::BASE_FONT_HELVETICA__BOLDOBLIQUE,
        ],
        DefaultFont::FONT_COURIER => [
            DefaultFont::STYLE_ROMAN => Type1::BASE_FONT_COURIER,
            DefaultFont::STYLE_OBLIQUE => Type1::BASE_FONT_COURIER__OBLIQUE,
            DefaultFont::STYLE_BOLD => Type1::BASE_FONT_COURIER__BOLD,
            DefaultFont::STYLE_BOLD_OBLIQUE => Type1::BASE_FONT_COURIER__BOLDOBLIQUE,
        ],
        DefaultFont::FONT_TIMES => [
            DefaultFont::STYLE_ROMAN => Type1::BASE_FONT_TIMES__ROMAN,
            DefaultFont::STYLE_ITALIC => Type1::BASE_FONT_TIMES__ITALIC,
            DefaultFont::STYLE_BOLD => Type1::BASE_FONT_TIMES__BOLD,
            DefaultFont::STYLE_BOLD_ITALIC => Type1::BASE_FONT_TIMES__BOLDITALIC,
        ],
        DefaultFont::FONT_ZAPFDINGBATS => [
            DefaultFont::STYLE_DEFAULT => Type1::BASE_FONT_ZAPFDINGBATS,
        ],
        DefaultFont::FONT_SYMBOL => [
            DefaultFont::STYLE_DEFAULT => Type1::BASE_FONT_SYMBOL,
        ],
    ];

    public function visitDocument(Structure2\Document $param)
    {
        $document = new Document();
    }
}