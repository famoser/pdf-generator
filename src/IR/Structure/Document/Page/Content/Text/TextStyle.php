<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Document\Page\Content\Text;

use PdfGenerator\IR\Structure\Document\Font;

readonly class TextStyle
{
    /**
     * Style constructor.
     */
    public function __construct(private Font $font, private float $fontSize, private float $lineHeight = 1)
    {
    }

    public function getFont(): Font
    {
        return $this->font;
    }

    public function getFontSize(): float
    {
        return $this->fontSize;
    }

    public function getLineHeight(): float
    {
        return $this->lineHeight;
    }
}
