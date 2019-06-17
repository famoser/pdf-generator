<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Configuration\State;

use PdfGenerator\Backend\Catalog\Font;
use PdfGenerator\Backend\Structure\Document\Page\State\TextState;

class TextStateRepository
{
    /**
     * @var Font
     */
    private $font = null;

    /**
     * @var float
     */
    private $fontSize = 8;

    /**
     * @var float
     */
    private $leading = 9.6;

    /**
     * @var TextState
     */
    private $activeTextState;

    /**
     * @param float $fontSize
     * @param float $lineHeight
     */
    public function setFontSize(float $fontSize, float $lineHeight = 1.2)
    {
        $this->fontSize = $fontSize;
        $this->leading = $this->fontSize * $lineHeight;

        $this->activeTextState = null;
    }

    /**
     * @param Font $font
     */
    public function setFont(Font $font): void
    {
        $this->font = $font;

        $this->activeTextState = null;
    }

    /**
     * @return TextState
     */
    public function getTextState()
    {
        if ($this->activeTextState !== null) {
            return $this->activeTextState;
        }

        $this->activeTextState = new TextState();
        $this->activeTextState->setFontSize($this->fontSize);
        $this->activeTextState->setLeading($this->leading);
        $this->activeTextState->setFont($this->font);

        return $this->activeTextState;
    }
}
