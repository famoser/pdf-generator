<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\IR;

use Famoser\PdfGenerator\Backend\Structure\Document\Xmp\DublinCoreElements;
use Famoser\PdfGenerator\Backend\Structure\Document\XmpMeta;
use Famoser\PdfGenerator\IR\Analysis\AnalyzeContentVisitor;
use Famoser\PdfGenerator\IR\Document\Meta;
use Famoser\PdfGenerator\IR\Document\Page;
use Famoser\PdfGenerator\IR\Document\Resource\DocumentResources;

class Document
{
    private ?Meta $meta = null;

    /**
     * @var Page[]
     */
    private array $pages = [];

    public function setMeta(Meta $meta): void
    {
        $this->meta = $meta;
    }

    public function addPage(Page $page): void
    {
        $this->pages[] = $page;
    }

    /**
     * @return Page[]
     */
    public function getPages(): array
    {
        return $this->pages;
    }

    public function render(): \Famoser\PdfGenerator\Backend\Structure\Document
    {
        $analyzeContentVisitor = new AnalyzeContentVisitor();
        foreach ($this->pages as $page) {
            foreach ($page->getContent() as $content) {
                $content->accept($analyzeContentVisitor);
            }
        }

        $analysisResult = $analyzeContentVisitor->getAnalysisResult();
        $documentVisitor = new DocumentVisitor($analysisResult);

        $meta = $this->meta ? $this->meta->visit($documentVisitor) : XmpMeta::createEmpty();
        $document = new \Famoser\PdfGenerator\Backend\Structure\Document($meta);

        $documentResources = new DocumentResources($documentVisitor);
        foreach ($this->pages as $page) {
            $page = $page->render($documentResources);
            $document->addPage($page);
        }

        return $document;
    }

    public function save(): string
    {
        return $this->render()->save();
    }
}
