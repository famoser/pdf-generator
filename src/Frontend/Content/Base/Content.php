<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Content\Base;

use PdfGenerator\Frontend\ContentVisitor;
use PdfGenerator\Frontend\MeasuredContent\Base\MeasuredContent;

abstract class Content
{
    abstract public function accept(ContentVisitor $contentVisitor): MeasuredContent;
}
