<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\CursorPrinter\Buffer\TextBuffer;

use PdfGenerator\IR\Document\Page\Content\Text\TextStyle;

class Phrase
{
    private string $text;

    private TextStyle $textStyle;

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getTextStyle(): TextStyle
    {
        return $this->textStyle;
    }

    public function setTextStyle(TextStyle $textStyle): void
    {
        $this->textStyle = $textStyle;
    }
}
