<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\FrontendResources\MeasuredContent\Base;

use PdfGenerator\FrontendResources\Allocator\Content\ContentAllocatorInterface;

abstract class MeasuredContent
{
    abstract public function createAllocator(): ContentAllocatorInterface;
}
