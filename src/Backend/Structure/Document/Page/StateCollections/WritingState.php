<?php

namespace Famoser\PdfGenerator\Backend\Structure\Document\Page\StateCollections;

use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\Base\BaseState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\ColorState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\TextState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\StateCollections\Base\BaseStateCollection;

readonly class WritingState extends BaseStateCollection
{
    public function __construct(private ColorState $colorState, private TextState $textState)
    {
    }

    /**
     * @return BaseState[]
     */
    public function getState(): array
    {
        return [$this->colorState, $this->textState];
    }

    public function getTextState(): TextState
    {
        return $this->textState;
    }
}
