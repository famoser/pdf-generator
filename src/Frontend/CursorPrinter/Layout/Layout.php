<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\CursorPrinter\Layout;

use PdfGenerator\Frontend\CursorPrinter\Cursor;

class Layout
{
    private Cursor $cursor;

    public function getCursor(): Cursor
    {
        return $this->cursor;
    }

    public function setCursor(Cursor $cursor): void
    {
        $this->cursor = $cursor;
    }

    public function moveRight(float $width): Cursor
    {
        $this->cursor = $this->getCursor()->moveRight($width);

        return $this->cursor;
    }

    public function moveDown(float $height): Cursor
    {
        $this->cursor = $this->getCursor()->moveDown($height);

        return $this->cursor;
    }

    public function moveRightDown(float $width, float $height): Cursor
    {
        $this->cursor = $this->cursor->moveRightDown($width, $height);

        return $this->cursor;
    }
}
