<?php

namespace Famoser\PdfGenerator\Backend\Structure\Document\Page\StateCollections;

use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\ColorState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\GeneralGraphicState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\TextState;

readonly class FullState
{
    public function __construct(private ?GeneralGraphicState $generalGraphicsState, private ?ColorState $colorState, private ?TextState $textState)
    {
    }

    public function cloneWithTextState(TextState $newTextState): self
    {
        return new self($this->generalGraphicsState, $this->colorState, $newTextState);
    }

    public function getGeneralGraphicsState(): ?GeneralGraphicState
    {
        return $this->generalGraphicsState;
    }

    public function getColorState(): ?ColorState
    {
        return $this->colorState;
    }

    public function getTextState(): ?TextState
    {
        return $this->textState;
    }
}
