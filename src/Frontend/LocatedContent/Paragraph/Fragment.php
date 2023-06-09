<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\LocatedContent\Paragraph;

use PdfGenerator\Frontend\Content\Style\TextStyle;

class Fragment
{
    /**
     * Fragment constructor.
     */
    public function __construct(private readonly string $text, private readonly TextStyle $textStyle, private readonly float $width)
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

    public function getWidth(): float
    {
        return $this->width;
    }
}
