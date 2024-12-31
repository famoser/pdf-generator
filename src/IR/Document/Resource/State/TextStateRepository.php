<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\IR\Document\Resource\State;

use Famoser\PdfGenerator\Backend\Structure\Document\Font;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\TextState;

class TextStateRepository
{
    private ?Font $font = null;

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

    private ?TextState $activeTextState = null;

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

    public function setWordSpace(float $wordSpace): void
    {
        if ($this->wordSpace !== $wordSpace) {
            $this->wordSpace = $wordSpace;
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

        $this->activeTextState = new TextState($this->font, $this->fontSize, $this->leading, $this->wordSpace, $this->charSpace, $this->scale);

        return $this->activeTextState;
    }
}
