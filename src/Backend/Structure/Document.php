<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure;

use PdfGenerator\Backend\Catalog\Catalog;
use PdfGenerator\Backend\Catalog\Page as CatalogPage;
use PdfGenerator\Backend\Catalog\Pages;
use PdfGenerator\Backend\Structure\Document\DocumentResources;
use PdfGenerator\Backend\Structure\Document\Page;
use PdfGenerator\Backend\Structure\Optimization\Configuration;

class Document
{
    /**
     * @var Page[]
     */
    private array $pages = [];

    private Configuration $configuration;

    public function __construct()
    {
        $this->configuration = new Configuration();
    }

    public function addPage(Page $page): void
    {
        $this->pages[] = $page;
    }

    public function render(): Catalog
    {
        $documentVisitor = new DocumentVisitor($this->configuration);
        $documentResources = new DocumentResources($documentVisitor);

        /** @var CatalogPage[] $pageEntries */
        $pageEntries = [];
        foreach ($this->pages as $page) {
            $pageEntries[] = $page->render($documentResources);
        }

        $pages = new Pages($pageEntries);

        return new Catalog($pages);
    }

    public function save(): string
    {
        return $this->render()->save();
    }

    public function setConfiguration(Configuration $configuration): void
    {
        $this->configuration = $configuration;
    }
}
