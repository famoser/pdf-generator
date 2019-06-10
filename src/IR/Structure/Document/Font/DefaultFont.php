<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Font;

use PdfGenerator\IR\DocumentVisitor;
use PdfGenerator\IR\Structure\Font;

class DefaultFont extends Font
{
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
     * @var string
     */
    private $font;

    /**
     * @var string
     */
    private $style;

    /**
     * DefaultFont constructor.
     *
     * @param string $font
     * @param string $style
     */
    public function __construct(string $font, string $style)
    {
        $this->font = $font;
        $this->style = $style;
    }

    /**
     * @param DocumentVisitor $visitor
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function accept(DocumentVisitor $visitor)
    {
        return $visitor->visitDefaultFont($this);
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->font . '_' . $this->style;
    }

    /**
     * @return string
     */
    public function getFont(): string
    {
        return $this->font;
    }

    /**
     * @return string
     */
    public function getStyle(): string
    {
        return $this->style;
    }

    /**
     * sets the encoding used by the font.
     *
     * @param string $escaped
     *
     * @return string
     */
    public function encode(string $escaped): string
    {
        return mb_convert_encoding($escaped, 'Windows-1252', 'UTF-8');
    }
}
