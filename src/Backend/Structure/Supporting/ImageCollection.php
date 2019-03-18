<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Supporting;

use PdfGenerator\Backend\Structure\Image;
use PdfGenerator\Backend\Structure\Resources;

class ImageCollection
{
    /**
     * @var Resources
     */
    private $resources;

    /**
     * @var Image[]
     */
    private $imageCache = [];

    /**
     * ImageCollection constructor.
     *
     * @param Resources $resources
     */
    public function __construct(Resources $resources)
    {
        $this->resources = $resources;
    }

    /**
     * @param string $imagePath
     *
     * @return Image
     */
    public function getOrCreateImage(string $imagePath)
    {
        if (!isset($this->fontCache[$imagePath])) {
            $this->imageCache[$imagePath] = $this->resources->addImage($imagePath);
        }

        return $this->imageCache[$imagePath];
    }
}
