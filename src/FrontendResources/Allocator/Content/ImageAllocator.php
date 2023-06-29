<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\FrontendResources\Allocator\Content;

use PdfGenerator\Frontend\Content\Style\ImageStyle;
use PdfGenerator\Frontend\Font\MeasuredContent\Image;
use PdfGenerator\FrontendResources\Size;

class ImageAllocator implements ContentAllocatorInterface
{
    private readonly ImageStyle $imageStyle;

    public function __construct(private readonly Image $image)
    {
        $this->imageStyle = $image->getImage();
    }

    public function allocate(string $maxWidth, string $maxHeight): Size
    {
        $image = $this->image->getImage();

        if (ImageStyle::SIZE_CONTAIN === $this->imageStyle->getSize()) {
            $widthAspect = $maxWidth / $image->getWidth();
            $heightAspect = $maxHeight / $image->getHeight();
            $aspect = min($widthAspect, $heightAspect);
        } else {
            throw new \Exception('Unsupported size');
        }

        return new Size($maxWidth * $aspect, $maxHeight * $aspect);
    }

    public function minimalWidth(): float
    {
        return 0;
    }

    public function widthEstimate(): float
    {
        return $this->image->getImage()->getWidth();
    }
}
