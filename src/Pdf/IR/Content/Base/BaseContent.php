<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\IR\Content\Base;

use Pdf\Backend\Object\Base\BaseObject;
use Pdf\Backend\Structure\File;
use Pdf\IR\ContentVisitor;

abstract class BaseContent
{
    /**
     * @param ContentVisitor $visitor
     * @param File $file
     *
     * @return BaseObject
     */
    abstract public function accept(ContentVisitor $visitor, File $file): BaseObject;
}
