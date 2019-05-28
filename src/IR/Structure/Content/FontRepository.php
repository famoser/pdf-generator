<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Content;

use PdfGenerator\Backend\Document;
use PdfGenerator\Backend\Structure\Font\Type1;

class FontRepository
{
    /**
     * @var Document
     */
    private $document;

    /**
     * @var Type1[]
     */
    private $type1FontCache;

    /**
     * @var string[][]
     */
    private $defaultFonts = [
        self::FONT_HELVETICA => [
            self::STYLE_DEFAULT => Type1::BASE_FONT_HELVETICA,
            self::STYLE_OBLIQUE => Type1::BASE_FONT_HELVETICA__OBLIQUE,
            self::STYLE_BOLD => Type1::BASE_FONT_HELVETICA__BOLD,
            self::STYLE_BOLD_OBLIQUE => Type1::BASE_FONT_HELVETICA__BOLDOBLIQUE,
        ],
        self::FONT_COURIER => [
            self::STYLE_ROMAN => Type1::BASE_FONT_COURIER,
            self::STYLE_OBLIQUE => Type1::BASE_FONT_COURIER__OBLIQUE,
            self::STYLE_BOLD => Type1::BASE_FONT_COURIER__BOLD,
            self::STYLE_BOLD_OBLIQUE => Type1::BASE_FONT_COURIER__BOLDOBLIQUE,
        ],
        self::FONT_TIMES => [
            self::STYLE_ROMAN => Type1::BASE_FONT_TIMES__ROMAN,
            self::STYLE_ITALIC => Type1::BASE_FONT_TIMES__ITALIC,
            self::STYLE_BOLD => Type1::BASE_FONT_TIMES__BOLD,
            self::STYLE_BOLD_ITALIC => Type1::BASE_FONT_TIMES__BOLDITALIC,
        ],
        self::FONT_ZAPFDINGBATS => [
            self::STYLE_DEFAULT => Type1::BASE_FONT_ZAPFDINGBATS,
        ],
        self::FONT_SYMBOL => [
            self::STYLE_DEFAULT => Type1::BASE_FONT_SYMBOL,
        ],
    ];

    const FONT_HELVETICA = 'Helvetica';
    const FONT_COURIER = 'Courier';
    const FONT_TIMES = 'Times';
    const FONT_SYMBOL = 'Symbol';
    const FONT_ZAPFDINGBATS = 'ZapfDingbats';

    const STYLE_DEFAULT = self::STYLE_ROMAN;
    const STYLE_ROMAN = 'ROMAN';
    const STYLE_ITALIC = 'ITALIC';
    const STYLE_BOLD = 'BOLD';
    const STYLE_OBLIQUE = 'OBLIQUE';
    const STYLE_BOLD_OBLIQUE = 'BOLD_OBLIQUE';
    const STYLE_BOLD_ITALIC = 'BOLD_ITALIC';

    /**
     * FontRepository constructor.
     *
     * @param Document $document
     */
    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    /**
     * @param string $font
     * @param string $style
     *
     *@throws \Exception
     *
     * @return Type1
     */
    public function getDefaultFont(string $font, string $style)
    {
        if (!\array_key_exists($font, $this->defaultFonts)) {
            throw new \Exception('The font ' . $font . ' is not part of the default set.');
        }

        $defaultStyles = $this->defaultFonts[$font];
        if (!\array_key_exists($style, $defaultStyles)) {
            throw new \Exception('This font style ' . $style . ' is not part of the default set.');
        }

        return $this->getOrCreateType1Font($defaultStyles[$style]);
    }

    /**
     * @param string $subtype
     * @param string $baseFont
     *
     * @return Type1
     */
    private function getOrCreateType1Font(string $baseFont)
    {
        $cacheKey = $baseFont;
        if (!isset($this->type1FontCache[$cacheKey])) {
            $this->type1FontCache[$cacheKey] = $this->document->getResourcesBuilder()->getResources()->addType1Font($baseFont);
        }

        return $this->type1FontCache[$cacheKey];
    }
}
