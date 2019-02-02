<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\Backend\Structure\Base;

use Pdf\Backend\StructureVisitor;

abstract class BaseStructure
{
    /**
     * @param StructureVisitor $visitor
     *
     * @return string
     */
    abstract public function accept(StructureVisitor $visitor): string;
}
