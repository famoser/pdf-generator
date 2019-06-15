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

use PdfGenerator\Backend\Structure\Document\Image;
use PdfGenerator\Backend\Structure\Font\EmbeddedFont;
use PdfGenerator\Backend\Structure\Page;
use PdfGenerator\IR\Structure\Analysis\AnalysisResult;
use PdfGenerator\IR\Structure\Font\DefaultFont;
use PdfGenerator\IR\Structure\Optimization\Configuration;
use PdfGenerator\IR\Structure\Optimization\FontOptimizer;
use PdfGenerator\IR\Structure\Optimization\ImageOptimizer;
use PdfGenerator\IR\Structure\PageContent\ToBackendContentVisitor;
use PdfGenerator\IR\Transformation\Document\Font\DefaultFontMapping;
use PdfGenerator\IR\Transformation\DocumentResources;
use PdfGenerator\IR\Transformation\PageResources;

class DocumentVisitor
{
    /**
     * @var DocumentResources
     */
    private $documentResources;

    /**
     * @var ImageOptimizer
     */
    private $imageOptimizer;

    /**
     * @var FontOptimizer
     */
    private $fontOptimizer;

    /**
     * @var AnalysisResult
     */
    private $analysisResult;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * DocumentStructureVisitor constructor.
     *
     * @param AnalysisResult $analysisResult
     * @param Configuration $configuration
     */
    public function __construct(AnalysisResult $analysisResult, Configuration $configuration)
    {
        $this->analysisResult = $analysisResult;
        $this->configuration = $configuration;

        $this->documentResources = new DocumentResources($this);
        $this->imageOptimizer = new ImageOptimizer();
        $this->fontOptimizer = new FontOptimizer();
    }

    /**
     * @param DefaultFont $param
     *
     * @throws \Exception
     *
     * @return \PdfGenerator\Backend\Structure\Font\DefaultFont
     */
    public function visitDefaultFont(DefaultFont $param)
    {
        $baseFont = $this->getDefaultFontBaseFont($param->getFont(), $param->getStyle());
        $encoding = \PdfGenerator\Backend\Structure\Font\DefaultFont::ENCODING_WIN_ANSI_ENCODING;

        return new \PdfGenerator\Backend\Structure\Font\DefaultFont($baseFont, $encoding);
    }

    /**
     * @param string $font
     * @param string $style
     *
     * @throws \Exception
     *
     * @return string
     */
    private function getDefaultFontBaseFont(string $font, string $style): string
    {
        if (!\array_key_exists($font, DefaultFontMapping::$defaultFontMapping)) {
            throw new \Exception('The font ' . $font . ' is not part of the default set.');
        }

        $styles = DefaultFontMapping::$defaultFontMapping[$font];
        if (!\array_key_exists($style, $styles)) {
            throw new \Exception('This font style ' . $style . ' is not part of the default set.');
        }

        return $styles[$style];
    }

    /**
     * @param Structure\Font\EmbeddedFont $param
     *
     * @throws \Exception
     *
     * @return EmbeddedFont
     */
    public function visitEmbeddedFont(Structure\Font\EmbeddedFont $param)
    {
        $text = $this->analysisResult->getTextPerFont($param);

        return new EmbeddedFont(EmbeddedFont::ENCODING_UTF_8, $content, $text);
    }

    /**
     * @param Structure\Image $param
     *
     * @return Image
     */
    public function visitImage(Structure\Image $param)
    {
        $imageData = file_get_contents($param->getImagePath());
        $extension = pathinfo($param->getImagePath(), PATHINFO_EXTENSION);

        $maxSize = $this->analysisResult->getMaxSizePerImage($param);

        return new Image($imageData, $extension, $maxSize->getWidth(), $maxSize->getHeight());
    }

    /**
     * @param Structure\Page $param
     *
     * @return Page
     */
    public function visitPage(Structure\Page $param)
    {
        $mediaBox = [0, 0, 210, 297];

        $page = new Page($mediaBox);

        $pageResources = new PageResources($this->documentResources);
        $contentVisitor = new ToBackendContentVisitor($pageResources);
        foreach ($param->getContent() as $item) {
            $content = $item->accept($contentVisitor);
            $page->addContent($content);
        }

        $page->setFonts($pageResources->getFonts());
        $page->setImages($pageResources->getImages());

        return $page;
    }
}
