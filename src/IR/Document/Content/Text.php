<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Document\Content;

use PdfGenerator\IR\Document\Content\Base\BaseContent;
use PdfGenerator\IR\Document\Content\Common\Position;
use PdfGenerator\IR\Document\Content\Text\TextStyle;

readonly class Text extends BaseContent
{
    public function __construct(private string $text, private Position $position, private TextStyle $style)
    {
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function getStyle(): TextStyle
    {
        return $this->style;
    }

    public function accept(ContentVisitorInterface $visitor)
    {
        return $visitor->visitText($this);
    }
}
