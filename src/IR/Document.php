<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR;

use PdfGenerator\IR\Analysis\AnalyzeContentVisitor;
use PdfGenerator\IR\Document\Page;
use PdfGenerator\IR\Document\Resource\DocumentResources;

class Document
{
    /**
     * @var Page[]
     */
    private array $pages = [];

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

    public function render(): \PdfGenerator\Backend\Structure\Document
    {
        $analyzeContentVisitor = new AnalyzeContentVisitor();
        foreach ($this->pages as $page) {
            foreach ($page->getContent() as $content) {
                $content->accept($analyzeContentVisitor);
            }
        }
        $analysisResult = $analyzeContentVisitor->getAnalysisResult();

        $document = new \PdfGenerator\Backend\Structure\Document();
        $documentVisitor = new DocumentVisitor($analysisResult);
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
