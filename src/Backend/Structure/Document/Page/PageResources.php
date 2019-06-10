<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Transformation;

use PdfGenerator\Backend\Catalog\Font;
use PdfGenerator\Backend\Catalog\Image;

class PageResources
{
    /**
     * @var DocumentResources
     */
    private $documentResources;

    /**
     * @var Font[]
     */
    private $fonts;

    /**
     * @var Image[]
     */
    private $images;

    /**
     * PageResources constructor.
     *
     * @param DocumentResources $documentResources
     */
    public function __construct(DocumentResources $documentResources)
    {
        $this->documentResources = $documentResources;
    }

    /**
     * @param \PdfGenerator\Backend\Structure\Font $structure
     *
     * @return Font
     */
    public function getFont(\PdfGenerator\Backend\Structure\Font $structure)
    {
        $font = $this->documentResources->getFont($structure);
        $this->fonts[$font->getIdentifier()] = $font;

        return $font;
    }

    /**
     * @param \PdfGenerator\IR\Structure\Image $structure
     *
     * @return Image
     */
    public function getImage(\PdfGenerator\IR\Structure\Image $structure)
    {
        $image = $this->documentResources->getImage($structure);
        $this->images[$image->getIdentifier()] = $image;

        return $image;
    }

    /**
     * @return Font[]
     */
    public function getFonts(): array
    {
        return $this->fonts;
    }

    /**
     * @return Image[]
     */
    public function getImages(): array
    {
        return $this->images;
    }
}
