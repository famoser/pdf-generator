<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Document;

use PdfGenerator\Backend\Catalog\Contents;
use PdfGenerator\Backend\Catalog\Pages;
use PdfGenerator\Backend\Catalog\Resources;
use PdfGenerator\Backend\Structure\Document\Page\Content\Base\BaseContent;
use PdfGenerator\Backend\Structure\Document\Page\ContentVisitor;

class Page
{
    /**
     * @var BaseContent[]
     */
    private array $content = [];

    /**
     * @var int[]
     */
    private array $mediaBox;

    /**
     * @var Font[]
     */
    private array $fonts;

    /**
     * @var Image[]
     */
    private array $images;

    /**
     * Page constructor.
     *
     * @param int[] $mediaBox
     */
    public function __construct(array $mediaBox)
    {
        $this->mediaBox = $mediaBox;
    }

    public function addContent(BaseContent $content)
    {
        $this->content[] = $content;
    }

    /**
     * @param Font[] $fonts
     */
    public function setFonts(array $fonts): void
    {
        $this->fonts = $fonts;
    }

    /**
     * @param Image[] $images
     */
    public function setImages(array $images): void
    {
        $this->images = $images;
    }

    public function render(Pages $parent, DocumentResources $documentResources): \PdfGenerator\Backend\Catalog\Page
    {
        $contentVisitor = new ContentVisitor($documentResources);

        $contentArray = [];
        foreach ($this->content as $item) {
            $content = $item->accept($contentVisitor);
            $contentArray[] = $content;
        }

        $contents = new Contents($contentArray);

        $resources = new Resources();
        foreach ($this->fonts as $font) {
            $mappedFont = $documentResources->getFont($font);
            $resources->addFont($mappedFont);
        }

        foreach ($this->images as $image) {
            $mappedImage = $documentResources->getImage($image);
            $resources->addImage($mappedImage);
        }

        return new \PdfGenerator\Backend\Catalog\Page($parent, $this->mediaBox, $resources, $contents);
    }
}
