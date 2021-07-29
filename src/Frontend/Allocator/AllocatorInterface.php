<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Allocator;

use PdfGenerator\Frontend\LocatedContent\Base\LocatedContent;

interface AllocatorInterface
{
    public function minimalWidth(): float;

    public function contentWidthEstimate(): float;

    /**
     * @return LocatedContent[]
     */
    public function place(float $maxWidth, float $maxHeight): array;
}
