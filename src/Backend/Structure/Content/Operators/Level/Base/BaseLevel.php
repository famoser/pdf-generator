<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Content\Operators\Level\Base;

use PdfGenerator\Backend\Content\Operators\LevelTransitionVisitor;

abstract class BaseLevel
{
    /**
     * @param LevelTransitionVisitor $visitor
     * @param self $previousState
     *
     * @return string[]
     */
    abstract public function accept(LevelTransitionVisitor $visitor, $previousState): array;
}
