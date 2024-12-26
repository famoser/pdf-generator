<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\Layout\Parts;

use Famoser\PdfGenerator\Frontend\Content\AbstractContent;
use Famoser\PdfGenerator\Frontend\Layout\AbstractBlock;
use Famoser\PdfGenerator\Frontend\Layout\ContentBlock;
use Famoser\PdfGenerator\Frontend\Layout\Style\BlockStyle;

class Row
{
    /**
     * @var AbstractBlock[]
     */
    private array $columns = [];

    private ?BlockStyle $style = null;

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

    public function setStyle(BlockStyle $style): self
    {
        $this->style = $style;

        return $this;
    }

    /**
     * @return AbstractBlock[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getStyle(): ?BlockStyle
    {
        return $this->style;
    }
}
