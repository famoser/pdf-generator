<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\File\Structure\Base;

use PdfGenerator\Backend\File\StructureVisitor;

abstract class BaseStructure
{
    abstract public function accept(StructureVisitor $visitor): string;
}
