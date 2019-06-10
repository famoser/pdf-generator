<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Content\Base;

use PdfGenerator\Backend\Content\Operators\State\Base\BaseState;
use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\Structure\ContentVisitor;

abstract class BaseContent
{
    /**
     * @param ContentVisitor $visitor
     *
     * @return BaseObject
     */
    abstract public function accept(ContentVisitor $visitor): BaseObject;

    /**
     * @return BaseState[]
     */
    abstract public function getInfluentialStates(): array;
}
