<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Document\Page\State;

use PdfGenerator\Backend\Structure\Document\Font;
use PdfGenerator\Backend\Structure\Document\Page\State\TextState;

class TextStateRepository
{
    private Font $font;

    private float $fontSize = 8;

    private float $leading = 8;

    /**
     * spacing between characters in unscaled text space.
     */
    private float $charSpace = 0;

    /**
     * spacing between words in unscaled text space.
     */
    private float $wordSpace = 0;

    /**
     * horizontal scaling in percentage.
     */
    private float $scale = 100;

    private TextState $activeTextState;

    public function setFontSize(float $fontSize): void
    {
        if ($this->fontSize !== $fontSize) {
            $this->fontSize = $fontSize;
            $this->activeTextState = null;
        }
    }

    public function setLeading(float $leading): void
    {
        if ($this->leading !== $leading) {
            $this->leading = $leading;
            $this->activeTextState = null;
        }
    }

    public function setFont(Font $font): void
    {
        if ($this->font !== $font) {
            $this->font = $font;
            $this->activeTextState = null;
        }
    }

    public function getTextState(): TextState
    {
        if (null !== $this->activeTextState) {
            return $this->activeTextState;
        }

        $this->activeTextState = new TextState();
        $this->activeTextState->setFontSize($this->fontSize);
        $this->activeTextState->setLeading($this->leading);
        $this->activeTextState->setFont($this->font);
        $this->activeTextState->setCharSpace($this->charSpace);
        $this->activeTextState->setWordSpace($this->wordSpace);
        $this->activeTextState->setScale($this->scale);

        return $this->activeTextState;
    }
}
