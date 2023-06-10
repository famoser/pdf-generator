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

use PdfGenerator\Backend\Catalog\Content as CatalogContent;
use PdfGenerator\Backend\Catalog\Contents;
use PdfGenerator\Backend\Catalog\Font as CatalogFont;
use PdfGenerator\Backend\Catalog\Image as CatalogImage;
use PdfGenerator\Backend\Catalog\Page as CatalogPage;
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
     * @var Font[]
     */
    private array $fonts = [];

    /**
     * @var Image[]
     */
    private array $images = [];

    /**
     * @param int[] $mediaBox
     */
    public function __construct(private readonly array $mediaBox)
    {
    }

    public function addContent(BaseContent $content): void
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

    public function render(DocumentResources $documentResources): CatalogPage
    {
        $contentVisitor = new ContentVisitor($documentResources);

        /** @var CatalogContent[] $contentEntries */
        $contentEntries = [];
        foreach ($this->content as $content) {
            $contentEntries[] = $content->accept($contentVisitor);
        }

        $contents = new Contents($contentEntries);

        /** @var CatalogFont[] $fonts */
        $fonts = [];
        foreach ($this->fonts as $font) {
            $fonts[] = $documentResources->getFont($font);
        }

        /** @var CatalogImage[] $images */
        $images = [];
        foreach ($this->images as $image) {
            $images[] = $documentResources->getImage($image);
        }

        $resources = new Resources($fonts, $images);

        return new CatalogPage($this->mediaBox, $resources, $contents);
    }
}
