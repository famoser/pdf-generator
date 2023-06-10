<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\CursorPrinter\Text;

use PdfGenerator\IR\Document\Content\Rectangle\RectangleStyle;
use PdfGenerator\IR\Document\Content\Text\TextStyle;

trait StyleGetSetTrait
{
    public function getTextStyle(): TextStyle
    {
        return $this->printer->getTextStyle();
    }

    public function setTextStyle(TextStyle $textStyle): void
    {
        $this->printer->setTextStyle($textStyle);
    }

    public function getRectangleStyle(): RectangleStyle
    {
        return $this->printer->getRectangleStyle();
    }

    public function setRectangleStyle(RectangleStyle $rectangleStyle): void
    {
        $this->printer->setRectangleStyle($rectangleStyle);
    }
}
