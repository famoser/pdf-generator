<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Block\Paragraph;

use PdfGenerator\Frontend\Content\Style\TextStyle;

class Phrase
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var TextStyle
     */
    private $textStyle;

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
