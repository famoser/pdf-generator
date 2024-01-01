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
use PdfGenerator\IR\Document\Content\Common\Size;
use PdfGenerator\IR\Document\Content\Rectangle\RectangleStyle;

readonly class Rectangle extends BaseContent
{
    public function __construct(private Position $position, private Size $size, private RectangleStyle $style)
    {
    }

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function getSize(): Size
    {
        return $this->size;
    }

    public function getStyle(): RectangleStyle
    {
        return $this->style;
    }

    public function accept(ContentVisitorInterface $visitor)
    {
        return $visitor->visitRectangle($this);
    }
}
