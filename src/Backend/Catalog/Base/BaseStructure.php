<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Catalog\Base;

use PdfGenerator\Backend\CatalogVisitor;
use PdfGenerator\Backend\File\Object\Base\BaseObject;

abstract class BaseStructure
{
    /**
     * @return BaseObject|BaseObject[]
     */
    abstract public function accept(CatalogVisitor $visitor): BaseObject|array;
}
