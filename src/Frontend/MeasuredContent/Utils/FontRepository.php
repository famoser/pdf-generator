<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\MeasuredContent\Utils;

use PdfGenerator\Frontend\Content\Style\Part\Font;
use PdfGenerator\Frontend\Content\Style\TextStyle;
use PdfGenerator\IR\Structure\Document\Font\DefaultFont;
use PdfGenerator\IR\Structure\Document\Font\EmbeddedFont;

class FontRepository
{
    /**
     * @var EmbeddedFont[]
     */
    private $embeddedFonts;

    /**
     * @var DefaultFont[]
     */
    private $defaultFonts;

    /**
     * @var string[][]
     */
    private static $weightStyleConverter = [
        Font::WEIGHT_NORMAL => [
            Font::STYLE_ROMAN => DefaultFont::STYLE_ROMAN,
            Font::STYLE_OBLIQUE => DefaultFont::STYLE_OBLIQUE,
            Font::STYLE_ITALIC => DefaultFont::STYLE_ITALIC,
        ],
        Font::WEIGHT_BOLD => [
            Font::STYLE_ROMAN => DefaultFont::STYLE_BOLD,
            Font::STYLE_OBLIQUE => DefaultFont::STYLE_BOLD_OBLIQUE,
            Font::STYLE_ITALIC => DefaultFont::STYLE_BOLD_ITALIC,
        ],
    ];

    public function getFontMeasurement(TextStyle $style): FontMeasurement
    {
        $font = $this->getFont($style->getFont());

        return new FontMeasurement($font, $style->getFontSize(), $style->getLineHeight());
    }

    public function getFont(Font $font)
    {
        if ($font->getSrc()) {
            return $this->getOrCreateEmbeddedFont($font->getSrc());
        }

        return $this->getOrCreateDefaultFont($font->getName(), $font->getWeight(), $font->getStyle());
    }

    private function getOrCreateDefaultFont(string $font, string $weight, string $style): DefaultFont
    {
        $weightStyle = self::$weightStyleConverter[$weight][$style];
        $key = $font . '_' . $weightStyle;
        if (!\array_key_exists($key, $this->defaultFonts)) {
            $this->defaultFonts[$key] = new DefaultFont($font, $weightStyle);
        }

        return $this->defaultFonts[$key];
    }

    /**
     * @throws \Exception
     */
    private function getOrCreateEmbeddedFont(string $fontPath): EmbeddedFont
    {
        if (!\array_key_exists($fontPath, $this->embeddedFonts)) {
            $font = EmbeddedFont::create($fontPath);

            $this->embeddedFonts[$fontPath] = $font;
        }

        return $this->embeddedFonts[$fontPath];
    }
}
