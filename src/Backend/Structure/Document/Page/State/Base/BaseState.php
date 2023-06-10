<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Document\Page\State\Base;

use PdfGenerator\Backend\Structure\Document\Page\Content\StateTransitionVisitor;

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
