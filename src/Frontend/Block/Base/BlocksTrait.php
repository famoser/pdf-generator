<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Block\Base;

use PdfGenerator\Frontend\Block\Block;

trait BlocksTrait
{
    /**
     * @var Block[]
     */
    private array $blocks = [];

    public function addBlock(BaseBlock $block): self
    {
        $this->blocks[] = $block;

        return $this;
    }

    /**
     * @return Block[]
     */
    public function getBlocks(): array
    {
        return $this->blocks;
    }
}
