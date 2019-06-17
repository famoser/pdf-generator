<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Page\Content\Base;

use PdfGenerator\IR\Structure\Page\ContentVisitor;

abstract class BaseContent
{
    /**
     * @param ContentVisitor $visitor
     *
     * @return \PdfGenerator\Backend\Structure\Page\Content\Base\BaseContent
     */
    abstract public function accept(ContentVisitor $visitor): \PdfGenerator\Backend\Structure\Page\Content\Base\BaseContent;
}
