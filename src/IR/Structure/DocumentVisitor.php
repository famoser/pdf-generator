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
use PdfGenerator\IR\Structure\Analysis\AnalysisResult;
use PdfGenerator\IR\Structure\Document\Font\DefaultFont;
use PdfGenerator\IR\Structure\Document\Font\DefaultFontType1Mapping;
use PdfGenerator\IR\Structure\Document\Font\EmbeddedFont;
use PdfGenerator\IR\Structure\Document\Font\FontVisitor;
use PdfGenerator\IR\Structure\Document\Image;

class DocumentVisitor implements FontVisitor
{
    /**
     * DocumentStructureVisitor constructor.
     */
    public function __construct(private readonly AnalysisResult $analysisResult)
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

        return new BackendImage($param->getData(), $type, $param->getWidth(), $param->getHeight(), (int)round($maxSize->getWidth()), (int)round($maxSize->getHeight()));
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
