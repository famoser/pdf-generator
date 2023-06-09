<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure;

use PdfGenerator\IR\Structure\Analysis\AnalysisResult;
use PdfGenerator\IR\Structure\Document\Font\DefaultFont;
use PdfGenerator\IR\Structure\Document\Font\EmbeddedFont;
use PdfGenerator\IR\Structure\Document\Image;
use PdfGenerator\IR\Structure\Document\Page;
use PdfGenerator\IR\Structure\Document\Page\AnalyzeContentVisitor;

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

    public function getOrCreateImage(string $imagePath): Image
    {
        if (!\array_key_exists($imagePath, $this->images)) {
            $image = Image::create($imagePath);

            $this->images[$imagePath] = $image;
        }

        return $this->images[$imagePath];
    }

    public function getOrCreateDefaultFont(string $font, string $style): DefaultFont
    {
        $key = $font.'_'.$style;
        if (!\array_key_exists($key, $this->defaultFonts)) {
            $this->defaultFonts[$key] = new DefaultFont($font, $style);
        }

        return $this->defaultFonts[$key];
    }

    /**
     * @throws \Exception
     */
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
        foreach ($this->pages as $page) {
            $page = $page->accept($documentVisitor);
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
