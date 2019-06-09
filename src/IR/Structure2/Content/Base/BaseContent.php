<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure2\Content\Base;

use PdfGenerator\Backend\Structure\Page;
use PdfGenerator\IR\Structure2\Content\ContentVisitor;

abstract class BaseContent
{
    /**
     * @param ContentVisitor $visitor
     * @param Page $page
     */
    abstract public function accept(ContentVisitor $visitor, Page $page);
}
