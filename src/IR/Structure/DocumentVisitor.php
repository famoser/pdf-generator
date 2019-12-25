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

use PdfGenerator\Backend\Structure\Document\Font\DefaultFont as BackendDefaultFont;
use PdfGenerator\Backend\Structure\Document\Font\EmbeddedFont as BackendEmbeddedFont;
use PdfGenerator\Backend\Structure\Document\Image as BackendImage;
use PdfGenerator\Backend\Structure\Document\Page as BackendPage;
use PdfGenerator\IR\Structure\Analysis\AnalysisResult;
use PdfGenerator\IR\Structure\Document\DocumentResources;
use PdfGenerator\IR\Structure\Document\Font\DefaultFont;
use PdfGenerator\IR\Structure\Document\Font\DefaultFontMapping;
use PdfGenerator\IR\Structure\Document\Font\EmbeddedFont;
use PdfGenerator\IR\Structure\Document\Image;
use PdfGenerator\IR\Structure\Document\Page;
use PdfGenerator\IR\Structure\Document\Page\PageResources;
use PdfGenerator\IR\Structure\Document\Page\ToBackendContentVisitor;

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
     */
    public function __construct(AnalysisResult $analysisResult)
    {
        $this->analysisResult = $analysisResult;

        $this->documentResources = new DocumentResources($this);
    }

    /**
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
     * @throws \Exception
     */
    private function getDefaultFontBaseFont(string $font, string $style): string
    {
        if (!\array_key_exists($font, DefaultFontMapping::$type1BaseFontMapping)) {
            throw new \Exception('The font ' . $font . ' is not part of the default set.');
        }

        $styles = DefaultFontMapping::$type1BaseFontMapping[$font];
        if (!\array_key_exists($style, $styles)) {
            throw new \Exception('This font style ' . $style . ' is not part of the default set.');
        }

        return $styles[$style];
    }

    /**
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
     * @throws \Exception
     *
     * @return BackendImage
     */
    public function visitImage(Image $param)
    {
        $imageData = file_get_contents($param->getImagePath());
        list($width, $height) = getimagesizefromstring($imageData);
        $type = self::getImageType($param->getImagePath());

        $maxSize = $this->analysisResult->getMaxSizePerImage($param);

        return new BackendImage($imageData, $type, $width, $height, $maxSize->getWidth(), $maxSize->getHeight());
    }

    /**
     * @throws \Exception
     *
     * @return string
     */
    private static function getImageType(string $imagePath)
    {
        $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
        switch ($extension) {
            case 'jpg':
                return BackendImage::TYPE_JPG;
            case 'jpeg':
                return BackendImage::TYPE_JPEG;
            case 'png':
                return BackendImage::TYPE_PNG;
            case 'gif':
                return BackendImage::TYPE_GIF;
            default:
                throw new \Exception('Image type not supported: ' . $extension . '. Use jpg, jpeg, png or gif');
        }
    }

    /**
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
