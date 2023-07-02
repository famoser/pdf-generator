<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout\Traits;

use PdfGenerator\Frontend\Content\AbstractContent;
use PdfGenerator\Frontend\Layout\AbstractBlock;
use PdfGenerator\Frontend\Layout\Block;
use PdfGenerator\Frontend\Layout\ContentBlock;

trait BlocksTrait
{
    /**
     * @var Block[]
     */
    private array $blocks = [];

    public function add(AbstractBlock $block): self
    {
        $this->blocks[] = $block;

        return $this;
    }

    public function addContent(AbstractContent $content): self
    {
        $this->blocks[] = new ContentBlock($content);

        return $this;
    }

    /**
     * @return Block[]
     */
    public function getBlocks(): array
    {
        return $this->blocks;
    }

    /**
     * @param AbstractBlock[] $blocks
     */
    public function cloneWithBlocks(array $blocks): self
    {
        $self = clone $this;
        $self->blocks = $blocks;

        return $self;
    }
}
