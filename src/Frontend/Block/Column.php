<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Block;

use PdfGenerator\Frontend\Allocator\ColumnAllocator;
use PdfGenerator\Frontend\Block\Base\Block;
use PdfGenerator\Frontend\Block\Style\ColumnStyle;

class Column extends Block
{
    /**
     * @var Block[]
     */
    private array $blocks = [];

    private ColumnStyle $style;

    /**
     * @param float[]|null $dimensions
     */
    public function __construct(ColumnStyle $style = null, array $dimensions = null)
    {
        parent::__construct($dimensions);

        $this->style = $style ?? new ColumnStyle();
    }

    public function addBlock(Block $cell)
    {
        $this->blocks[] = $cell;
    }

    public function getStyle(): ColumnStyle
    {
        return $this->style;
    }

    /**
     * @return Block[]
     */
    public function getBlocks(): array
    {
        return $this->blocks;
    }

    public function createAllocator(): ColumnAllocator
    {
        return new ColumnAllocator($this);
    }
}
