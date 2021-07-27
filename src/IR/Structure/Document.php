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

    public function addPage(Page $page)
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
            $data = file_get_contents($imagePath);
            list($width, $height) = getimagesizefromstring($data);
            $type = self::getImageType($imagePath);

            $this->images[$imagePath] = new Image($imagePath, $data, $type, $width, $height);
        }

        return $this->images[$imagePath];
    }

    /**
     * @throws \Exception
     */
    private static function getImageType(string $imagePath): string
    {
        /** @var string $extension */
        $extension = pathinfo($imagePath, \PATHINFO_EXTENSION);
        switch ($extension) {
            case 'jpg':
                return Image::TYPE_JPG;
            case 'jpeg':
                return Image::TYPE_JPEG;
            case 'png':
                return Image::TYPE_PNG;
            case 'gif':
                return Image::TYPE_GIF;
            default:
                throw new \Exception('Image type not supported: ' . $extension . '. Use jpg, jpeg, png or gif');
        }
    }

    public function getOrCreateDefaultFont(string $font, string $style): DefaultFont
    {
        $key = $font.'_'.$style;
        if (!\array_key_exists($key, $this->images)) {
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
            $fontData = file_get_contents($fontPath);

            $parser = Parser::create();
            $font = $parser->parse($fontData);

            $this->embeddedFonts[$fontPath] = new EmbeddedFont($fontPath, $fontData, $font);
        }

        return $this->embeddedFonts[$fontPath];
    }

    /**
     * @return \PdfGenerator\Backend\Structure\Document
     */
    public function render()
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

    /**
     * @return string
     */
    public function save()
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
