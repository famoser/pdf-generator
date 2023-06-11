<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\FrontendResources\MeasuredContent;

use PdfGenerator\Frontend\Content\Style\ImageStyle;
use PdfGenerator\FrontendResources\Allocator\Content\ContentAllocatorInterface;
use PdfGenerator\FrontendResources\Allocator\Content\ImageAllocator;
use PdfGenerator\FrontendResources\MeasuredContent\Base\MeasuredContent;

class Image extends MeasuredContent
{
    private readonly ImageStyle $style;

    public function __construct(private readonly \PdfGenerator\IR\Document\Resource\Image $image, ImageStyle $style = null)
    {
        $this->style = $style ?? new ImageStyle();
    }

    public function getImage(): \PdfGenerator\IR\Document\Resource\Image
    {
        return $this->image;
    }

    public function getStyle(): ImageStyle
    {
        return $this->style;
    }

    public function createAllocator(): ContentAllocatorInterface
    {
        return new ImageAllocator($this);
    }
}
