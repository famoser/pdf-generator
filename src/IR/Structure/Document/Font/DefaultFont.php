<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Document\Font;

use PdfGenerator\IR\Structure\Document\Font;

class DefaultFont extends Font
{
    public const FONT_HELVETICA = 'Helvetica';
    public const FONT_COURIER = 'Courier';
    public const FONT_TIMES = 'Times';
    public const FONT_SYMBOL = 'Symbol';
    public const FONT_ZAPFDINGBATS = 'ZapfDingbats';

    public const STYLE_DEFAULT = self::STYLE_ROMAN;
    public const STYLE_ROMAN = 'ROMAN';
    public const STYLE_ITALIC = 'ITALIC';
    public const STYLE_BOLD = 'BOLD';
    public const STYLE_OBLIQUE = 'OBLIQUE';
    public const STYLE_BOLD_OBLIQUE = 'BOLD_OBLIQUE';
    public const STYLE_BOLD_ITALIC = 'BOLD_ITALIC';

    private string $font;

    private string $style;

    /**
     * @var int[]
     */
    private array $size;

    /**
     * DefaultFont constructor.
     */
    public function __construct(string $font, string $style)
    {
        $this->font = $font;
        $this->style = $style;

        $this->size = DefaultFontSize::getSize($this->font, $this->style);
    }

    /**
     * @return mixed
     *
     * @throws \Exception
     */
    public function accept(FontVisitor $visitor)
    {
        return $visitor->visitDefaultFont($this);
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->font.'_'.$this->style;
    }

    public function getFont(): string
    {
        return $this->font;
    }

    public function getStyle(): string
    {
        return $this->style;
    }

    public function getUnitsPerEm()
    {
        return $this->size['unitsPerEm'];
    }

    public function getAscender()
    {
        return $this->size['ascender'];
    }

    public function getDescender()
    {
        return $this->size['descender'];
    }

    public function getLineGap()
    {
        return $this->size['lineGap'];
    }
}
