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
use Famoser\PdfGenerator\Frontend\Layout\AbstractElement;
use Famoser\PdfGenerator\Frontend\Layout\ContentBlock;
use Famoser\PdfGenerator\Frontend\Layout\Style\ElementStyle;

class Row
{
    /**
     * @var AbstractElement[]
     */
    private array $columns = [];

    private ?ElementStyle $style = null;

    public function set(int $index, AbstractElement $block): self
    {
        $this->columns[$index] = $block;

        return $this;
    }

    public function tryGet(int $index): ?AbstractElement
    {
        return $this->columns[$index] ?? null;
    }

    public function setStyle(ElementStyle $style): self
    {
        $this->style = $style;

        return $this;
    }

    /**
     * @return AbstractElement[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getStyle(): ?ElementStyle
    {
        return $this->style;
    }
}
