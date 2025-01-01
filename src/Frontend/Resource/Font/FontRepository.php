<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\Resource\Font;

use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;
use Famoser\PdfGenerator\Frontend\Layout\TextSpan;
use Famoser\PdfGenerator\Frontend\Resource\Font;
use Famoser\PdfGenerator\Frontend\Resource\Font\WordSizer\WordSizerInterface;
use Famoser\PdfGenerator\Frontend\Resource\Font\WordSizer\WordSizerVisitor;
use Famoser\PdfGenerator\IR\Document\Resource\Font\DefaultFont;
use Famoser\PdfGenerator\IR\Document\Resource\Font\EmbeddedFont;
use Famoser\PdfGenerator\Utils\SingletonTrait;

class FontRepository
{
    use SingletonTrait;

    /**
     * @var EmbeddedFont[]
     */
    private array $embeddedFonts = [];

    /**
     * @var DefaultFont[]
     */
    private array $defaultFonts = [];

    /**
     * @var WordSizerInterface[]
     */
    private array $wordSizerByFont = [];

    /**
     * @var string[][]
     */
    private static array $weightStyleConverter = [
        FontWeight::Normal->name => [
            FontStyle::Roman->name => DefaultFont::STYLE_ROMAN,
            FontStyle::Oblique->name => DefaultFont::STYLE_OBLIQUE,
            FontStyle::Italic->name => DefaultFont::STYLE_ITALIC,
        ],
        FontWeight::Bold->name => [
            FontStyle::Roman->name => DefaultFont::STYLE_BOLD,
            FontStyle::Oblique->name => DefaultFont::STYLE_BOLD_OBLIQUE,
            FontStyle::Italic->name => DefaultFont::STYLE_BOLD_ITALIC,
        ],
    ];

    /**
     * @var string[]
     */
    private static array $fontNameConverter = [
        FontFamily::Helvetica->name => DefaultFont::FONT_HELVETICA,
        FontFamily::Times->name => DefaultFont::FONT_TIMES,
        FontFamily::Courier->name => DefaultFont::FONT_COURIER,
        FontFamily::Symbol->name => DefaultFont::FONT_SYMBOL,
        FontFamily::ZapfDingbats->name => DefaultFont::FONT_ZAPFDINGBATS,
    ];

    public function getFont(Font $font): DefaultFont|EmbeddedFont
    {
        if ($font->getSrc()) {
            return $this->getOrCreateEmbeddedFont($font->getSrc());
        }

        return $this->getOrCreateDefaultFont($font->getFamily(), $font->getWeight(), $font->getStyle());
    }

    public function getFontMeasurement(TextSpan $span): FontMeasurement
    {
        $font = $this->getFont($span->getTextStyle()->getFont());
        if (!\array_key_exists($font->getIdentifier(), $this->wordSizerByFont)) {
            $wordSizerVisitor = new WordSizerVisitor();
            /** @var WordSizerInterface $wordSizer */
            $wordSizer = $font->acceptFont($wordSizerVisitor);
            $this->wordSizerByFont[$font->getIdentifier()] = $wordSizer;
        }

        $wordSizer = $this->wordSizerByFont[$font->getIdentifier()];

        return new FontMeasurement($font, $span->getFontSize(), $span->getLineHeight(), $wordSizer);
    }

    private function getOrCreateDefaultFont(FontFamily $font, FontWeight $weight, FontStyle $style): DefaultFont
    {
        $weightStyle = self::$weightStyleConverter[$weight->name][$style->name];
        $fontName = self::$fontNameConverter[$font->name];
        $key = $fontName.'_'.$weightStyle;
        if (!\array_key_exists($key, $this->defaultFonts)) {
            $this->defaultFonts[$key] = DefaultFont::create($fontName, $weightStyle);
        }

        return $this->defaultFonts[$key];
    }

    private function getOrCreateEmbeddedFont(string $fontPath): EmbeddedFont
    {
        if (!\array_key_exists($fontPath, $this->embeddedFonts)) {
            $font = EmbeddedFont::create($fontPath);

            $this->embeddedFonts[$fontPath] = $font;
        }

        return $this->embeddedFonts[$fontPath];
    }
}
