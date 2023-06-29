<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\LayoutEngine\Allocate;

use PdfGenerator\Frontend\Content\Rectangle;
use PdfGenerator\Frontend\LayoutEngine\AbstractContentVisitor;

/**
 * This allocates content on the PDF.
 *
 * All allocated content fits
 *
 * @implements AbstractContentVisitor<ContentAllocation|null>
 */
class ContentAllocationVisitor extends AbstractContentVisitor
{
    public function __construct(private readonly float $width, private readonly float $height)
    {
    }

    public function visitRectangle(Rectangle $rectangle): ?ContentAllocation
    {
        return new ContentAllocation($this->width, $this->height, $rectangle);
    }
}
