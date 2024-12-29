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

use Famoser\PdfGenerator\Backend\Structure\Document\Font\DefaultFont as BackendDefaultFont;
use Famoser\PdfGenerator\Backend\Structure\Document\Font\EmbeddedFont as BackendEmbeddedFont;
use Famoser\PdfGenerator\Backend\Structure\Document\Image as BackendImage;
use Famoser\PdfGenerator\Backend\Structure\Document\Xmp\DublinCoreElements;
use Famoser\PdfGenerator\Backend\Structure\Document\Xmp\Pdf;
use Famoser\PdfGenerator\Backend\Structure\Document\XmpMeta;
use Famoser\PdfGenerator\IR\Analysis\AnalysisResult;
use Famoser\PdfGenerator\IR\Document\Resource\Font\DefaultFont;
use Famoser\PdfGenerator\IR\Document\Resource\Font\EmbeddedFont;
use Famoser\PdfGenerator\IR\Document\Resource\Font\Utils\DefaultFontType1Mapping;
use Famoser\PdfGenerator\IR\Document\Resource\Image;

readonly class DocumentVisitor
{
    public function __construct(private AnalysisResult $analysisResult)
    {
    }

    /**
     * @throws \Exception
     */
    public function visitDefaultFont(DefaultFont $param): BackendDefaultFont
    {
        $baseFont = $this->getDefaultFontBaseFont($param->getFont(), $param->getStyle());

        return new BackendDefaultFont($baseFont);
    }

    /**
     * @throws \Exception
     */
    private function getDefaultFontBaseFont(string $font, string $style): string
    {
        if (!\array_key_exists($font, DefaultFontType1Mapping::$mapping)) {
            throw new \Exception('The font '.$font.' is not part of the default set.');
        }

        $styles = DefaultFontType1Mapping::$mapping[$font];
        if (!\array_key_exists($style, $styles)) {
            throw new \Exception('This font style '.$style.' is not part of the default set.');
        }

        return $styles[$style];
    }

    /**
     * @throws \Exception
     */
    public function visitEmbeddedFont(EmbeddedFont $param): BackendEmbeddedFont
    {
        $text = $this->analysisResult->getTextPerFont($param);

        return new BackendEmbeddedFont($param->getFontData(), $param->getFont(), $text);
    }

    public function visitImage(Image $param): BackendImage
    {
        $type = self::getImageType($param->getType());

        $maxSize = $this->analysisResult->getMaxSizePerImage($param);

        return new BackendImage($param->getData(), $type, $param->getWidth(), $param->getHeight(), (int) round($maxSize->getWidth()), (int) round($maxSize->getHeight()));
    }

    public function visitMeta(Document\Meta $param): XmpMeta
    {
        $languages = $param->getOtherLanguages();
        if ($param->getLanguage()) {
            array_unshift($languages, $param->getLanguage());
        }

        $mainLanguage = count($languages) > 0 ? $languages[0] : DublinCoreElements::DEFAULT_LANG;
        $title = $param->getTitleTranslations();
        if ($param->getTitle()) {
            $title = array_merge([$mainLanguage => $param->getTitle()], $title);
        }
        $description = $param->getDescriptionTranslations();
        if ($param->getDescription()) {
            $description = array_merge([$mainLanguage => $param->getDescription()], $description);
        }

        $pdf = new Pdf(implode("\n", $param->getKeywords()));
        $dublinCoreElements = new DublinCoreElements($languages, $title, $description, $param->getCreators(), $param->getContributors(), $param->getPublishers(), $param->getKeywords(), $param->getDates());

        return new XmpMeta($pdf, $dublinCoreElements);
    }

    private static function getImageType(string $type): string
    {
        return match ($type) {
            Image::TYPE_JPG => BackendImage::TYPE_JPG,
            Image::TYPE_JPEG => BackendImage::TYPE_JPEG,
            Image::TYPE_PNG => BackendImage::TYPE_PNG,
            Image::TYPE_GIF => BackendImage::TYPE_GIF,
            default => Image::TYPE_JPG,
        };
    }
}
