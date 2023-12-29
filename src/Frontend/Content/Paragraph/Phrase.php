<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Content\Paragraph;

use PdfGenerator\Frontend\Content\Style\TextStyle;

class Phrase
{
    public function __construct(private string $text, private TextStyle $textStyle)
    {
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return string[]
     */
    public function getLines(): array
    {
        $textWithNormalizedNewlines = str_replace(["\r\n", "\n\r", "\r"], "\n", $this->text);

        return explode("\n", $textWithNormalizedNewlines);
    }

    public function getTextStyle(): TextStyle
    {
        return $this->textStyle;
    }

    public function setTextStyle(TextStyle $textStyle): void
    {
        $this->textStyle = $textStyle;
    }

    /**
     * @param string[] $lines
     */
    public function cloneWithLines(array $lines): self
    {
        $self = clone $this;
        $self->text = implode("\n", $lines);

        return $self;
    }
}
