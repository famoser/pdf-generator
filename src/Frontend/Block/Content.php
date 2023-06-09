<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Block;

use PdfGenerator\Frontend\Allocator\AllocatorInterface;
use PdfGenerator\Frontend\Allocator\ContentAllocator;
use PdfGenerator\Frontend\Block\Base\Block;
use PdfGenerator\Frontend\Block\Style\Base\BlockStyle;
use PdfGenerator\Frontend\Block\Style\ContentStyle;
use PdfGenerator\Frontend\MeasuredContent\Base\MeasuredContent;

class Content extends Block
{
    private ContentStyle $style;

    /**
     * Content constructor.
     */
    public function __construct(private MeasuredContent $measuredContent, ContentStyle $style = null, array $dimensions = null)
    {
        parent::__construct($dimensions);
        $this->style = $style ?? new BlockStyle();
    }

    public function getStyle(): ContentStyle
    {
        return $this->style;
    }

    public function getMeasuredContent(): MeasuredContent
    {
        return $this->measuredContent;
    }

    public function createAllocator(): AllocatorInterface
    {
        return new ContentAllocator($this);
    }
}
