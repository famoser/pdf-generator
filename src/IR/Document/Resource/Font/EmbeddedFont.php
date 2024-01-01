<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Document\Resource\Font;

use PdfGenerator\Font\IR\Parser;
use PdfGenerator\IR\Document\Resource\Font;

readonly class EmbeddedFont extends Font
{
    public function __construct(private string $fontPath, private string $fontData, private \PdfGenerator\Font\IR\Structure\Font $font)
    {
    }

    public static function create(string $fontPath): self
    {
        $fontData = file_get_contents($fontPath);

        $parser = Parser::create();
        $font = $parser->parse($fontData);

        return new self($fontPath, $fontData, $font);
    }

    public function accept(FontVisitor $visitor)
    {
        return $visitor->visitEmbeddedFont($this);
    }

    public function getIdentifier(): string
    {
        return $this->fontPath;
    }

    public function getFontData(): string
    {
        return $this->fontData;
    }

    public function getFont(): \PdfGenerator\Font\IR\Structure\Font
    {
        return $this->font;
    }

    /**
     * top of text area until baseline.
     */
    public function getAscender(): int
    {
        return $this->font->getTableDirectory()->getOS2Table()->getSTypoAscender();
    }

    /**
     * bottom of text area until baseline
     * negative, as measured "the other way around".
     */
    public function getDescender(): int
    {
        return $this->font->getTableDirectory()->getOS2Table()->getSTypoDecender();
    }

    /**
     * Gap between two text areas below each others.
     */
    public function getLineGap(): int
    {
        return $this->font->getTableDirectory()->getOS2Table()->getSTypoLineGap();
    }

    /**
     * Scale the character coordinates are in.
     */
    public function getUnitsPerEm(): int
    {
        return $this->font->getTableDirectory()->getHeadTable()->getUnitsPerEm();
    }
}
