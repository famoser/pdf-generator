<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Document;

use PdfGenerator\Backend\Structure\Document\Page as BackendPage;
use PdfGenerator\IR\Document\Content\Base\BaseContent;
use PdfGenerator\IR\Document\Resource\DocumentResources;
use PdfGenerator\IR\Document\Resource\PageResources;

class Page
{
    /**
     * @var BaseContent[]
     */
    private array $content = [];

    /**
     * @param float[] $size
     */
    public function __construct(private readonly string $pageNumber, private readonly array $size)
    {
    }

    public function addContent(BaseContent $baseContent): void
    {
        $this->content[] = $baseContent;
    }

    public function getIdentifier(): string
    {
        return $this->pageNumber;
    }

    /**
     * @return BaseContent[]
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * @return float[]
     */
    public function getSize(): array
    {
        return $this->size;
    }

    public function render(DocumentResources $documentResources): BackendPage
    {
        $mediaBox = array_merge([0, 0], $this->getSize());

        $page = new BackendPage($mediaBox);

        $pageResources = new PageResources($documentResources);
        $contentVisitor = new ContentVisitor($pageResources);
        foreach ($this->getContent() as $item) {
            $content = $item->accept($contentVisitor);
            $page->addContent($content);
        }

        $page->setFonts($pageResources->getFonts());
        $page->setImages($pageResources->getImages());

        return $page;
    }
}
