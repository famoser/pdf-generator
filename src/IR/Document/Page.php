<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\IR\Document;

use Famoser\PdfGenerator\Backend\Structure\Document\Page as BackendPage;
use Famoser\PdfGenerator\IR\Document\Content\Base\BaseContent;
use Famoser\PdfGenerator\IR\Document\Page\PageProperties;
use Famoser\PdfGenerator\IR\Document\Page\PagePropertiesConverter;
use Famoser\PdfGenerator\IR\Document\Resource\DocumentResources;
use Famoser\PdfGenerator\IR\Document\Resource\PageResources;

class Page
{
    /**
     * @var BaseContent[]
     */
    private array $content = [];

    public function __construct(private readonly string $pageNumber, private readonly PageProperties $properties)
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

    public function getProperties(): PageProperties
    {
        return $this->properties;
    }

    public function render(DocumentResources $documentResources): BackendPage
    {
        // TODO: rethink structure
        $properties = PagePropertiesConverter::convert($this->properties);
        $page = new BackendPage($this->properties->getMediaBox(), $properties);

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
