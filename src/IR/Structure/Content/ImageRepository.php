<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Content;

use PdfGenerator\Backend\Document;
use PdfGenerator\Backend\Structure\Image;

class ImageRepository
{
    /**
     * @var Document
     */
    private $document;

    /**
     * @var Image[]
     */
    private $imageCache;

    /**
     * FontRepository constructor.
     *
     * @param Document $document
     */
    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    /**
     * @param string $imagePath
     *
     * @return Image
     */
    public function getImage(string $imagePath)
    {
        return $this->getOrCreateImage($imagePath);
    }

    /**
     * @param string $imagePath
     *
     * @return Image
     */
    private function getOrCreateImage(string $imagePath)
    {
        $cacheKey = $imagePath;
        if (!isset($this->imageCache[$cacheKey])) {
            $this->imageCache[$cacheKey] = $this->document->getResourcesBuilder()->getResources()->addImage($imagePath);
        }

        return $this->imageCache[$cacheKey];
    }
}
