<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout\Parts;

use PdfGenerator\Frontend\Content\AbstractContent;
use PdfGenerator\Frontend\Layout\AbstractBlock;
use PdfGenerator\Frontend\Layout\ContentBlock;

class Row
{
    /**
     * @var AbstractBlock[]
     */
    private array $columns = [];

    public function set(int $index, AbstractBlock $block): self
    {
        $this->columns[$index] = $block;

        return $this;
    }

    public function setContent(int $index, AbstractContent $content): self
    {
        $this->columns[$index] = new ContentBlock($content);

        return $this;
    }

    public function tryGet(int $index): ?AbstractBlock
    {
        return $this->columns[$index] ?? null;
    }

    /**
     * @return AbstractBlock[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }
}
