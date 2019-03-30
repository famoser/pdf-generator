<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Content\Symbols;

use PdfGenerator\Backend\Content\Operators\Level\TextLevel;

class TextSymbol
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var TextLevel
     */
    private $textLevel;

    /**
     * TextSymbol constructor.
     *
     * @param string $content
     * @param TextLevel $textLevel
     */
    public function __construct(string $content, TextLevel $textLevel)
    {
        $this->content = $content;
        $this->textLevel = $textLevel;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return TextLevel
     */
    public function getTextLevel(): TextLevel
    {
        return $this->textLevel;
    }
}
