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

use PdfGenerator\IR\Cursor;

trait CursorGetSetTrait
{
    public function getCursor(): Cursor
    {
        return $this->getPrinter()->getCursor();
    }

    public function setCursor(Cursor $cursor)
    {
        $this->getPrinter()->setCursor($cursor);
    }

    public function moveRight(float $width)
    {
        return $this->getPrinter()->moveRight($width);
    }

    public function moveDown(float $height)
    {
        return $this->getPrinter()->moveDown($height);
    }

    public function moveRightDown(float $width, float $height)
    {
        return $this->getPrinter()->moveRightDown($width, $height);
    }
}
