<?php

namespace Famoser\PdfGenerator\Frontend\Content;

use Famoser\PdfGenerator\Frontend\Resource\Image;

readonly class ImagePlacement extends AbstractContent
{
    public function __construct(private float $width, private float $height, private Image $image)
    {
        parent::__construct($this->width, $this->height);
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    public function accept(ContentVisitorInterface $contentVisitor): void
    {
        $contentVisitor->visitImagePlacement($this);
    }
}
