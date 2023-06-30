<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Resource\Image;

use PdfGenerator\IR\Document\Resource\Image;
use PdfGenerator\Utils\SingletonTrait;

class ImageRepository
{
    use SingletonTrait;

    /**
     * @var Image[]
     */
    private array $images = [];

    public function getOrCreateImage(string $imagePath, string $type): Image
    {
        if (!\array_key_exists($imagePath, $this->images)) {
            $image = Image::create($imagePath, $type);

            $this->images[$imagePath] = $image;
        }

        return $this->images[$imagePath];
    }
}
