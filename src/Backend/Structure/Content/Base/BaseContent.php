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

use PdfGenerator\Backend\File\File;
use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\Structure\ContentVisitor;
use PdfGenerator\Backend\Structure\Page;

abstract class BaseContent
{
    /**
     * @param ContentVisitor $visitor
     * @param File $file
     * @param Page $page
     *
     * @return BaseObject
     */
    abstract public function accept(ContentVisitor $visitor, File $file, Page $page): BaseObject;
}