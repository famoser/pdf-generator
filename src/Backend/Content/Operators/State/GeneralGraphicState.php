<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Content\Operators\State;

use PdfGenerator\Backend\Content\Operators\StateTransitionVisitor;
use PdfGenerator\Backend\File\Structure\Base\BaseState;

class GeneralGraphicState extends BaseState
{
    /**
     * @param StateTransitionVisitor $visitor
     * @param self $previousState
     *
     * @return string[]
     */
    public function accept(StateTransitionVisitor $visitor, self $previousState): array
    {
        return $visitor->visitGeneralGraphics($this, $previousState);
    }
}
