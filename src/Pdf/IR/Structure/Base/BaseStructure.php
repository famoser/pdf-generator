<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\IR\Structure\Base;

use Pdf\Backend\File;
use Pdf\Backend\Object\Base\BaseObject;
use Pdf\IR\StructureVisitor;

abstract class BaseStructure
{
    /**
     * @param StructureVisitor $visitor
     * @param File $file
     *
     * @return BaseObject
     */
    abstract public function accept(StructureVisitor $visitor, File $file): BaseObject;
}
