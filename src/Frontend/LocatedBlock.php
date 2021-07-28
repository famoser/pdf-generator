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

use PdfGenerator\Frontend\Block\Base\Block;

class LocatedBlock
{
    /**
     * @var Cursor
     */
    private $cursor;

    /**
     * @var Block
     */
    private $block;

    /**
     * LocatedBlock constructor.
     */
    public function __construct(Cursor $cursor, Block $block)
    {
        $this->cursor = $cursor;
        $this->block = $block;
    }

    public function getCursor(): Cursor
    {
        return $this->cursor;
    }

    public function getBlock(): Block
    {
        return $this->block;
    }
}
