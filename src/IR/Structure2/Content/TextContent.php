<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure2\Content;

use PdfGenerator\IR\Structure2\Content\Common\Position;
use PdfGenerator\IR\Structure2\Content\Text\Style;

class TextContent
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var Position
     */
    private $position;

    /**
     * @var Style
     */
    private $style;

    /**
     * TextPlacement constructor.
     *
     * @param string $text
     * @param Position $position
     * @param Style $style
     */
    public function __construct(string $text, Position $position, Style $style)
    {
        $this->text = $text;
        $this->position = $position;
        $this->style = $style;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return Position
     */
    public function getPosition(): Position
    {
        return $this->position;
    }

    /**
     * @return Style
     */
    public function getStyle(): Style
    {
        return $this->style;
    }
}
