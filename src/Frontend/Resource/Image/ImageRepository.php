<?php

namespace Famoser\PdfGenerator\Frontend\Resource\Image;

use Famoser\PdfGenerator\IR\Document\Resource\Image;
use Famoser\PdfGenerator\Utils\SingletonTrait;

class ImageRepository
{
    use SingletonTrait;

    /**
     * @var Image[]
     */
    private array $images = [];

    public function getImage(\Famoser\PdfGenerator\Frontend\Resource\Image $image): Image
    {
        if (!\array_key_exists($image->getSrc(), $this->images)) {
            $image = Image::create($image->getSrc(), $image->getType());

            $this->images[$image->getSrc()] = $image;
        }

        return $this->images[$image->getSrc()];
    }
}
