<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Printer;

use PdfGenerator\IR\Structure\Document\Page\Content\Text\TextStyle;

class Fragment
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var TextStyle
     */
    private $textStyle;

    /**
     * @var float
     */
    private $width;

    /**
     * Fragment constructor.
     */
    public function __construct(string $text, TextStyle $textStyle, float $width)
    {
        $this->text = $text;
        $this->textStyle = $textStyle;
        $this->width = $width;
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
