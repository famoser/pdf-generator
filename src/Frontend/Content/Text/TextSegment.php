<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\Content\Text;

use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;

readonly class TextSegment
{
    public function __construct(private string $text, private TextStyle $textStyle)
    {
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getTextStyle(): TextStyle
    {
        return $this->textStyle;
    }
}
