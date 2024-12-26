<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\IR\Document\Content\Text;

readonly class Phrase
{
    public function __construct(private string $text, private TextStyle $style)
    {
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getStyle(): TextStyle
    {
        return $this->style;
    }
}
