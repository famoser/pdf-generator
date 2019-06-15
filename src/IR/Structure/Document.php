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

use PdfGenerator\Font\IR\Parser;
use PdfGenerator\IR\DocumentVisitor;
use PdfGenerator\IR\Structure\Analysis\AnalysisResult;
use PdfGenerator\IR\Structure\Document\Page\AnalyzeContentVisitor;
use PdfGenerator\IR\Structure\Font\DefaultFont;
use PdfGenerator\IR\Structure\Font\EmbeddedFont;
use PdfGenerator\IR\Structure\Optimization\Configuration;

class Document
{
    /**
     * @var Page[]
     */
    private $pages = [];

    /**
     * @var Image[]
     */
    private $images = [];

    /**
     * @var DefaultFont[]
     */
    private $defaultFonts = [];

    /**
     * @var EmbeddedFont[]
     */
    private $embeddedFonts = [];

    /**
     * @param int $pageNumber
     *
     * @return Page
     */
    public function getOrCreatePage(int $pageNumber): Page
    {
        $maxPageNumber = \count($this->pages);

        while ($pageNumber > $maxPageNumber) {
            $this->pages[] = new Page($maxPageNumber);
            ++$maxPageNumber;
        }

        return $this->pages[$pageNumber - 1];
    }

    /**
     * @param string $imagePath
     *
     * @return Image
     */
    public function getOrCreateImage(string $imagePath): Image
    {
        if (!\array_key_exists($imagePath, $this->images)) {
            $this->images[$imagePath] = new Image($imagePath);
        }

        return $this->images[$imagePath];
    }

    /**
     * @param string $font
     * @param string $style
     *
     * @return DefaultFont
     */
    public function getOrCreateDefaultFont(string $font, string $style): DefaultFont
    {
        $key = $font . '_' . $style;
        if (!\array_key_exists($key, $this->images)) {
            $this->defaultFonts[$key] = new DefaultFont($font, $style);
        }

        return $this->defaultFonts[$key];
    }

    /**
     * @param string $fontPath
     *
     * @throws \Exception
     *
     * @return EmbeddedFont
     */
    public function getOrCreateEmbeddedFont(string $fontPath): EmbeddedFont
    {
        if (!\array_key_exists($fontPath, $this->embeddedFonts)) {
            $content = file_get_contents($fontPath);

            $parser = Parser::create();
            $font = $parser->parse($content);

            $this->embeddedFonts[$fontPath] = new EmbeddedFont($fontPath, $font);
        }

        return $this->embeddedFonts[$fontPath];
    }

    /**
     * @param Configuration $configuration
     *
     * @return \PdfGenerator\Backend\Structure\Document
     */
    public function render(Configuration $configuration)
    {
        $analysisResult = $this->analyze();

        $document = new \PdfGenerator\Backend\Structure\Document();
        $documentVisitor = new DocumentVisitor($analysisResult, $configuration);
        foreach ($this->pages as $page) {
            $page = $page->accept($documentVisitor);
            $document->addPage($page);
        }

        return $document;
    }

    /**
     * @param Configuration $configuration
     *
     * @return string
     */
    public function save(Configuration $configuration)
    {
        return $this->render($configuration)->save();
    }

    /**
     * @return AnalysisResult
     */
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
