<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Document\Font;

use PdfGenerator\Font\IR\Parser;
use PdfGenerator\IR\Structure\Document\Font;

class EmbeddedFont extends Font
{
    /**
     * @var string
     */
    private $fontPath;

    /**
     * @var string
     */
    private $fontData;

    /**
     * @var \PdfGenerator\Font\IR\Structure\Font
     */
    private $font;

    /**
     * EmbeddedFont constructor.
     */
    public function __construct(string $fontPath, string $fontData, \PdfGenerator\Font\IR\Structure\Font $font)
    {
        $this->fontPath = $fontPath;
        $this->fontData = $fontData;
        $this->font = $font;
    }

    /**
     * @throws \Exception
     */
    public static function create(string $fontPath): self
    {
        $fontData = file_get_contents($fontPath);

        $parser = Parser::create();
        $font = $parser->parse($fontData);

        return new self($fontPath, $fontData, $font);
    }

    /**
     * @throws \Exception
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function accept(FontVisitor $visitor)
    {
        return $visitor->visitEmbeddedFont($this);
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->fontPath;
    }

    /**
     * @return string
     */
    public function getFontData()
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
    public function getAscender()
    {
        return $this->font->getTableDirectory()->getOS2Table()->getSTypoAscender();
    }

    /**
     * bottom of text area until baseline
     * negative, as measured "the other way around".
     */
    public function getDescender()
    {
        return $this->font->getTableDirectory()->getOS2Table()->getSTypoDecender();
    }

    /**
     * Gap between two text areas below each others.
     */
    public function getLineGap()
    {
        return $this->font->getTableDirectory()->getOS2Table()->getSTypoLineGap();
    }

    /**
     * Scale the character coordinates are in.
     */
    public function getUnitsPerEm()
    {
        return $this->font->getTableDirectory()->getHeadTable()->getUnitsPerEm();
    }
}
