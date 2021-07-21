<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Layout;

use PdfGenerator\IR\Cursor;

class Layout
{
    /**
     * @var Cursor
     */
    private $cursor;

    public function getCursor(): Cursor
    {
        return $this->cursor;
    }

    public function setCursor(Cursor $cursor)
    {
        $this->cursor = $cursor;
    }

    public function moveRight(float $width)
    {
        $this->cursor = $this->getCursor()->moveRight($width);

        return $this->cursor;
    }

    public function moveDown(float $height)
    {
        $this->cursor = $this->getCursor()->moveDown($height);

        return $this->cursor;
    }

    public function moveRightDown(float $width, float $height)
    {
        $this->cursor = $this->cursor->moveRightDown($width, $height);

        return $this->cursor;
    }
}
