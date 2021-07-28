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

use PdfGenerator\Frontend\Content\Image;

class ImageRepository
{
    /**
     * @var \PdfGenerator\IR\Structure\Document\Image[]
     */
    private $imageCache;

    public function getImage(Image $param)
    {
        if (!\array_key_exists($param->getSrc(), $this->imageCache)) {
            $image = \PdfGenerator\IR\Structure\Document\Image::create($param->getSrc());

            $this->imageCache[$param->getSrc()] = $image;
        }

        return $this->imageCache[$param->getSrc()];
    }
}
