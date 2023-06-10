<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Document\Font;

use PdfGenerator\IR\Document\Font;
use PdfGenerator\IR\Document\Font\Utils\DefaultFontSizeLookup;

readonly class DefaultFont extends Font
{
    final public const FONT_HELVETICA = 'Helvetica';
    final public const FONT_COURIER = 'Courier';
    final public const FONT_TIMES = 'Times';
    final public const FONT_SYMBOL = 'Symbol';
    final public const FONT_ZAPFDINGBATS = 'ZapfDingbats';

    final public const STYLE_DEFAULT = self::STYLE_ROMAN;
    final public const STYLE_ROMAN = 'ROMAN';
    final public const STYLE_ITALIC = 'ITALIC';
    final public const STYLE_BOLD = 'BOLD';
    final public const STYLE_OBLIQUE = 'OBLIQUE';
    final public const STYLE_BOLD_OBLIQUE = 'BOLD_OBLIQUE';
    final public const STYLE_BOLD_ITALIC = 'BOLD_ITALIC';

    /**
     * @var int[]
     */
    private array $size;

    public function __construct(private string $font, private string $style)
    {
        $this->size = DefaultFontSizeLookup::getSize($this->font, $this->style);
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

    public function getIdentifier(): string
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
