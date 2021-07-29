<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Allocator\ContentAllocator;

use PdfGenerator\Frontend\Content\Style\ImageStyle;
use PdfGenerator\Frontend\MeasuredContent\Image;
use PdfGenerator\Frontend\Size;

class ImageAllocator
{
    /**
     * @var Image
     */
    private $image;

    /**
     * @var ImageStyle
     */
    private $imageStyle;

    /**
     * ImageAllocator constructor.
     */
    public function __construct(Image $image, ImageStyle $imageStyle)
    {
        $this->image = $image;
        $this->imageStyle = $imageStyle;
    }

    public function allocate(string $maxWidth, string $maxHeight)
    {
        $image = $this->image->getImage();

        if ($this->imageStyle->getSize() === ImageStyle::SIZE_CONTAIN) {
            $widthAspect = $maxWidth / $image->getWidth();
            $heightAspect = $maxHeight / $image->getHeight();
            $aspect = min($widthAspect, $heightAspect);
        } else {
            throw new \Exception('Unsupported size');
        }

        return new Size($maxWidth * $aspect, $maxHeight * $aspect);
    }
}
