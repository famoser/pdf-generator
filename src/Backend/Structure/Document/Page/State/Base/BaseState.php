<?php

namespace Famoser\PdfGenerator\Backend\Structure\Document\Page\State\Base;

use Famoser\PdfGenerator\Backend\Structure\Document\Page\StateTransitionVisitor;

abstract readonly class BaseState
{
    /**
     * @return string[]
     */
    abstract public function accept(StateTransitionVisitor $visitor): array;

    public function stateIdentifier(): string
    {
        return static::class;
    }
}
