<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Content\Text;

class TextSymbol
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var TextState
     */
    private $textState;

    /**
     * TextSymbol constructor.
     *
     * @param string $content
     * @param TextState $textState
     */
    public function __construct(string $content, TextState $textState)
    {
        $this->content = $content;
        $this->textState = $textState;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return TextState
     */
    public function getTextState(): TextState
    {
        return $this->textState;
    }
}
