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

use PdfGenerator\IR\Structure\Document\Page\Content\Rectangle\RectangleStyle;
use PdfGenerator\IR\Structure\Document\Page\Content\Text\TextStyle;

trait StyleGetSetTrait
{
    public function getTextStyle(): TextStyle
    {
        return $this->getPrinter()->getTextStyle();
    }

    public function setTextStyle(TextStyle $textStyle)
    {
        $this->getPrinter()->setTextStyle($textStyle);
    }

    public function getRectangleStyle(): RectangleStyle
    {
        return $this->getPrinter()->getRectangleStyle();
    }

    public function setRectangleStyle(RectangleStyle $rectangleStyle): void
    {
        $this->getPrinter()->setRectangleStyle($rectangleStyle);
    }
}
