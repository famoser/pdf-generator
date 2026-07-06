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
use Famoser\PdfGenerator\Backend\Catalog\Pages;
use Famoser\PdfGenerator\Backend\Structure\Document\DocumentResources;
use Famoser\PdfGenerator\Backend\Structure\Document\Info;
use Famoser\PdfGenerator\Backend\Structure\Document\Page;
use Famoser\PdfGenerator\Backend\Structure\Document\XmpMeta;
use Famoser\PdfGenerator\Backend\Structure\Optimization\Configuration;

class Document
{
    /**
     * @var Page[]
     */
    private array $pages = [];

    public function __construct(private readonly ?Info $info, private readonly ?XmpMeta $meta)
    {
    }

    public function addPage(Page $page): void
    {
        $this->pages[] = $page;
    }

    public function render(?Configuration $configuration = null): Catalog
    {
        $documentVisitor = new DocumentVisitor($configuration ?? new Configuration());
        $meta = $this->meta?->accept($documentVisitor);
        $objectValues = $this->info?->accept($documentVisitor);

        $documentResources = new DocumentResources($documentVisitor);
        $pageEntries = [];
        foreach ($this->pages as $page) {
            $pageEntries[] = $page->render($documentResources);
        }

        $pages = new Pages($pageEntries);

        return new Catalog($pages, $meta, $objectValues);
    }

    public function save(): string
    {
        return $this->render()->save();
    }
}
