<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\MeasuredContent;

use PdfGenerator\Frontend\Allocator\Content\ContentAllocatorInterface;
use PdfGenerator\Frontend\Allocator\Content\ImageAllocator;
use PdfGenerator\Frontend\Content\Style\ImageStyle;
use PdfGenerator\Frontend\MeasuredContent\Base\MeasuredContent;

class Image extends MeasuredContent
{
    /**
     * @var \PdfGenerator\IR\Structure\Document\Image
     */
    private $image;

    /**
     * @var ImageStyle
     */
    private $style;

    /**
     * Image constructor.
     */
    public function __construct(\PdfGenerator\IR\Structure\Document\Image $image, ImageStyle $style = null)
    {
        $this->image = $image;
        $this->style = $style ?? new ImageStyle();
    }

    public function getImage(): \PdfGenerator\IR\Structure\Document\Image
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
