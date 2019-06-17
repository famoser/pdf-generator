<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Base;

use PdfGenerator\Backend\Catalog\Content;
use PdfGenerator\Backend\Structure\Document\Page\ContentVisitor;
use PdfGenerator\Backend\Structure\Operators\State\Base\BaseState;

abstract class BaseContent
{
    /**
     * @param ContentVisitor $visitor
     *
     * @return Content
     */
    abstract public function accept(ContentVisitor $visitor): Content;

    /**
     * @return BaseState[]
     */
    abstract public function getInfluentialStates(): array;
}
