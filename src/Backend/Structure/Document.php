<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\Structure;

use Famoser\PdfGenerator\Backend\Catalog\Catalog;
use Famoser\PdfGenerator\Backend\Catalog\Page as CatalogPage;
use Famoser\PdfGenerator\Backend\Catalog\Pages;
use Famoser\PdfGenerator\Backend\Structure\Document\DocumentResources;
use Famoser\PdfGenerator\Backend\Structure\Document\Page;
use Famoser\PdfGenerator\Backend\Structure\Document\XmpMeta;
use Famoser\PdfGenerator\Backend\Structure\Optimization\Configuration;

class Document
{
    /**
     * @var Page[]
     */
    private array $pages = [];

    private readonly Configuration $configuration;

    public function __construct(private readonly ?XmpMeta $meta)
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
        $meta = $this->meta?->accept($documentVisitor);

        $documentResources = new DocumentResources($documentVisitor);
        $pageEntries = [];
        foreach ($this->pages as $page) {
            $pageEntries[] = $page->render($documentResources);
        }

        $pages = new Pages($pageEntries);

        return new Catalog($pages, $meta);
    }

    public function save(): string
    {
        return $this->render()->save();
    }
}
