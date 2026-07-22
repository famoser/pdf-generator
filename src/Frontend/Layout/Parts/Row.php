<?php

namespace Famoser\PdfGenerator\Frontend\Layout\Parts;

use Famoser\PdfGenerator\Frontend\Layout\AbstractElement;
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
