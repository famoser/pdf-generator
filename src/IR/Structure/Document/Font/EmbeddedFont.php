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
     *
     * @return mixed
     */
    public function accept(FontVisitor $visitor)
    {
        return $visitor->visitEmbeddedFont($this);
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->fontPath;
    }

    /**
     * @return mixed
     */
    public function getFontData()
    {
        return $this->fontData;
    }

    public function getFont(): \PdfGenerator\Font\IR\Structure\Font
    {
        return $this->font;
    }
}
