<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
