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
use PdfGenerator\IR\Structure\DocumentVisitor;

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
     */
    public function __construct(string $font, string $style)
    {
        $this->font = $font;
        $this->style = $style;
    }

    /**
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

    public function getFont(): string
    {
        return $this->font;
    }

    public function getStyle(): string
    {
        return $this->style;
    }
}
