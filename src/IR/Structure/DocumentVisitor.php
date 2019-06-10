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
use PdfGenerator\Font\Backend\FileWriter;
use PdfGenerator\Font\IR\Parser;
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
        $orderedCodepoints = $this->fontOptimizer->getOrderedCodepoints($text);

        $parser = Parser::create();
        $fontContent = file_get_contents($param->getFontPath());
        $font = $parser->parse($fontContent);

        $fontSubset = $this->fontOptimizer->getFontSubset($font, $orderedCodepoints);

        $writer = FileWriter::create();
        $content = $writer->writeFont($fontSubset);

        $widths = [];
        foreach ($fontSubset->getCharacters() as $character) {
            $widths[] = $character->getLongHorMetric()->getAdvanceWidth();
        }

        $characterMappings = $this->fontOptimizer->getCharacterMappings($orderedCodepoints);

        // TODO: need to parse name table to fix this
        $fontName = 'SomeFont';

        return new EmbeddedFont($fontName, $content, $characterMappings, $widths);
    }

    /**
     * @param Structure\Image $param
     *
     * @return Image
     */
    public function visitImage(Structure\Image $param)
    {
        list($width, $height) = getimagesize($param->getImagePath());
        $imageData = $this->getImageData($param, $width, $height);

        return new Image($imageData, $width, $height, \PdfGenerator\Backend\Catalog\Image::IMAGE_TYPE_JPEG);
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

    /**
     * @param int $width
     * @param int $height
     * @param Structure\PageContent\Common\Size $maxSize
     *
     * @return int[]
     */
    private function getTargetHeightWidth(int $width, int $height, Structure\PageContent\Common\Size $maxSize): array
    {
        $dpi = $this->configuration->getAutoResizeImagesDpi();
        $maxWidth = $maxSize->getWidth() * $dpi;
        $maxHeight = $maxSize->getHeight() * $dpi;

        // if wider than needed, resize such that width = maxWidth
        if ($width > $maxWidth) {
            $smallerBy = $maxWidth / (float)$width;
            $width = $maxWidth;
            $height = $height * $smallerBy;
        }

        // if height is lower, resize such that height = maxHeight
        if ($height < $maxHeight) {
            $biggerBy = $maxHeight / (float)$height;
            $height = $maxHeight;
            $width = $width * $biggerBy;
        }

        return [$width, $height];
    }

    /**
     * @param Structure\Image $param
     * @param int $width
     * @param int $height
     *
     * @return false|string
     */
    private function getImageData(Structure\Image $param, int $width, int $height)
    {
        if ($this->configuration->getAutoResizeImages()) {
            $maxSize = $this->analysisResult->getMaxSizePerImage($param);

            list($targetWidth, $targetHeight) = $this->getTargetHeightWidth($width, $height, $maxSize);

            if ($targetWidth < $width) {
                return $this->imageOptimizer->resize($param->getImagePath(), $targetWidth, $targetHeight);
            }
        }

        return file_get_contents($param->getImagePath());
    }
}
