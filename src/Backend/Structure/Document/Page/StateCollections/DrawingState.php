<?php

namespace Famoser\PdfGenerator\Backend\Structure\Document\Page\StateCollections;

use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\Base\BaseState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\ColorState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\GeneralGraphicState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\StateCollections\Base\BaseStateCollection;

readonly class DrawingState extends BaseStateCollection
{
    public function __construct(private GeneralGraphicState $generalGraphicsState, private ColorState $colorState)
    {
    }

    public function getGeneralGraphicsState(): GeneralGraphicState
    {
        return $this->generalGraphicsState;
    }

    /**
     * @return BaseState[]
     */
    public function getState(): array
    {
        return [$this->generalGraphicsState, $this->colorState];
    }
}
