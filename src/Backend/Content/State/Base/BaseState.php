<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Content\Operators\State\Base;

use PdfGenerator\Backend\Content\StateTransitionVisitor;

abstract class BaseState
{
    /**
     * @param StateTransitionVisitor $visitor
     *
     * @return string[]
     */
    abstract public function accept(StateTransitionVisitor $visitor): array;

    /**
     * @return string
     */
    public function stateIdentifier()
    {
        return \get_class($this);
    }
}
