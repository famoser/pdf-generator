<?php

namespace Famoser\PdfGenerator\Backend\Structure\Document\Page\Content\Paragraph;

use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\Base\BaseState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\TextState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\StateCollections\WritingState;

readonly class TextSegment
{
    public function __construct(private string $text, private WritingState $writingState)
    {
    }

    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return BaseState[]
     */
    public function getInfluentialStates(): array
    {
        return $this->writingState->getState();
    }

    public function getTextState(): TextState
    {
        return $this->writingState->getTextState();
    }
}
