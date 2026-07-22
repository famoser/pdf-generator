<?php

namespace Famoser\PdfGenerator\Backend\Structure\Document\Page\StateCollections\Base;

use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\Base\BaseState;

abstract readonly class BaseStateCollection
{
    /**
     * @return BaseState[]
     */
    abstract public function getState(): array;
}
