<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Base;

use PdfGenerator\IR\StructureVisitor;

abstract class BaseStructure2
{
    /**
     * @param StructureVisitor $visitor
     *
     * @return mixed
     */
    abstract public function accept(StructureVisitor $visitor);
}
