<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\PageContent\Text;

use PdfGenerator\IR\Structure\Font;

class TextStyle
{
    /**
     * @var Font
     */
    private $font;

    /**
     * @var float
     */
    private $fontSize;

    /**
     * Style constructor.
     *
     * @param Font $font
     * @param float $fontSize
     */
    public function __construct(Font $font, float $fontSize)
    {
        $this->font = $font;
        $this->fontSize = $fontSize;
    }

    /**
     * @return Font
     */
    public function getFont(): Font
    {
        return $this->font;
    }

    /**
     * @return float
     */
    public function getFontSize(): float
    {
        return $this->fontSize;
    }
}
