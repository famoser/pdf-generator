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

use PdfGenerator\IR\Analysis\AnalysisResult;
use PdfGenerator\IR\Analysis\AnalyzeContentVisitor;
use PdfGenerator\IR\Document\Page;
use PdfGenerator\IR\Document\Resource\DocumentResources;
use PdfGenerator\IR\Document\Resource\Font\DefaultFont;
use PdfGenerator\IR\Document\Resource\Font\EmbeddedFont;
use PdfGenerator\IR\Document\Resource\Image;

class Document
{
    /**
     * @var Page[]
     */
    private array $pages = [];

    /**
     * @var Image[]
     */
    private array $images = [];

    /**
     * @var DefaultFont[]
     */
    private array $defaultFonts = [];

    /**
     * @var EmbeddedFont[]
     */
    private array $embeddedFonts = [];

    public function addPage(Page $page): void
    {
        $this->pages[] = $page;
    }

    public function getPage(int $pageIndex): Page
    {
        return $this->getPages()[$pageIndex];
    }

    /**
     * @return Page[]
     */
    public function getPages(): array
    {
        return $this->pages;
    }

    public function getOrCreateImage(string $imagePath, string $type): Image
    {
        if (!\array_key_exists($imagePath, $this->images)) {
            $image = Image::create($imagePath, $type);

            $this->images[$imagePath] = $image;
        }

        return $this->images[$imagePath];
    }

    /**
     * @throws \JsonException
     */
    public function getOrCreateDefaultFont(string $font, string $style): DefaultFont
    {
        $key = $font.'_'.$style;
        if (!\array_key_exists($key, $this->defaultFonts)) {
            $this->defaultFonts[$key] = DefaultFont::create($font, $style);
        }

        return $this->defaultFonts[$key];
    }

    public function getOrCreateEmbeddedFont(string $fontPath): EmbeddedFont
    {
        if (!\array_key_exists($fontPath, $this->embeddedFonts)) {
            $font = EmbeddedFont::create($fontPath);

            $this->embeddedFonts[$fontPath] = $font;
        }

        return $this->embeddedFonts[$fontPath];
    }

    public function render(): \PdfGenerator\Backend\Structure\Document
    {
        $analysisResult = $this->analyze();

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

    private function analyze(): AnalysisResult
    {
        $analyzeContentVisitor = new AnalyzeContentVisitor();

        foreach ($this->pages as $page) {
            foreach ($page->getContent() as $content) {
                $content->accept($analyzeContentVisitor);
            }
        }

        return $analyzeContentVisitor->getAnalysisResult();
    }
}
