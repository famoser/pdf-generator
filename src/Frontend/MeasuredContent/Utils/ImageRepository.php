<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\MeasuredContent\Utils;

use PdfGenerator\Frontend\Content\ImagePlacement;

class ImageRepository
{
    /**
     * @var \PdfGenerator\IR\Document\Resource\Image[]
     */
    private array $imageCache = [];

    public function getImage(ImagePlacement $param): \PdfGenerator\IR\Document\Resource\Image
    {
        if (!\array_key_exists($param->getSrc(), $this->imageCache)) {
            $image = \PdfGenerator\IR\Document\Resource\Image::create($param->getSrc());

            $this->imageCache[$param->getSrc()] = $image;
        }

        return $this->imageCache[$param->getSrc()];
    }
}
