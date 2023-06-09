<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend;

class Cursor
{
    /**
     * Cursor constructor.
     */
    public function __construct(private readonly float $left, private readonly float $top, private readonly int $pageIndex)
    {
    }

    public function getLeft(): float
    {
        return $this->left;
    }

    public function getTop(): float
    {
        return $this->top;
    }

    public function getPageIndex(): int
    {
        return $this->pageIndex;
    }

    public static function moveRightDown(self $cursor, float $right, float $down): Cursor
    {
        return new self($cursor->left + $right, $cursor->top + $down, $cursor->pageIndex);
    }
}
