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

use PdfGenerator\Backend\Structure\Document\Font\DefaultFont as BackendDefaultFont;
use PdfGenerator\Backend\Structure\Document\Font\EmbeddedFont as BackendEmbeddedFont;
use PdfGenerator\Backend\Structure\Document\Image as BackendImage;
use PdfGenerator\Backend\Structure\Document\Page as BackendPage;
use PdfGenerator\IR\Structure\Analysis\AnalysisResult;
use PdfGenerator\IR\Structure\Document\DocumentResources;
use PdfGenerator\IR\Structure\Document\Font\DefaultFont;
use PdfGenerator\IR\Structure\Document\Font\EmbeddedFont;
use PdfGenerator\IR\Structure\Document\Image;
use PdfGenerator\IR\Structure\Document\Page;
use PdfGenerator\IR\Structure\Document\Page\PageResources;
use PdfGenerator\IR\Structure\Page\ToBackendContentVisitor;
use PdfGenerator\IR\Transformation\Document\Font\DefaultFontMapping;

class DocumentVisitor
{
    /**
     * @var DocumentResources
     */
    private $documentResources;

    /**
     * @var AnalysisResult
     */
    private $analysisResult;

    /**
     * DocumentStructureVisitor constructor.
     *
     * @param AnalysisResult $analysisResult
     */
    public function __construct(AnalysisResult $analysisResult)
    {
        $this->analysisResult = $analysisResult;

        $this->documentResources = new DocumentResources($this);
    }

    /**
     * @param DefaultFont $param
     *
     * @throws \Exception
     *
     * @return BackendDefaultFont
     */
    public function visitDefaultFont(DefaultFont $param)
    {
        $baseFont = $this->getDefaultFontBaseFont($param->getFont(), $param->getStyle());
        $encoding = BackendDefaultFont::ENCODING_WIN_ANSI_ENCODING;

        return new BackendDefaultFont($baseFont, $encoding);
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
     * @param EmbeddedFont $param
     *
     * @throws \Exception
     *
     * @return BackendEmbeddedFont
     */
    public function visitEmbeddedFont(EmbeddedFont $param)
    {
        $text = $this->analysisResult->getTextPerFont($param);

        return new BackendEmbeddedFont(BackendEmbeddedFont::ENCODING_UTF_8, $param->getFont(), $text);
    }

    /**
     * @param Image $param
     *
     * @return BackendImage
     */
    public function visitImage(Image $param)
    {
        $imageData = file_get_contents($param->getImagePath());
        list($width, $height) = getimagesizefromstring($imageData);
        $extension = pathinfo($param->getImagePath(), PATHINFO_EXTENSION);

        $maxSize = $this->analysisResult->getMaxSizePerImage($param);

        return new BackendImage($imageData, $extension, $width, $height, $maxSize->getWidth(), $maxSize->getHeight());
    }

    /**
     * @param Page $param
     *
     * @return BackendPage
     */
    public function visitPage(Page $param)
    {
        $mediaBox = [0, 0, 210, 297];

        $page = new BackendPage($mediaBox);

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
