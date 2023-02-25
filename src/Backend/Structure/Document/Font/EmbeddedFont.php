<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Document\Font;

use PdfGenerator\Backend\Structure\Document\Font;
use PdfGenerator\Backend\Structure\DocumentVisitor;

class EmbeddedFont extends Font
{
    /**
     * @var \PdfGenerator\Font\IR\Structure\Font
     */
    private $font;

    /**
     * @var string
     */
    private $fontData;

    /**
     * @var string
     */
    private $charactersUsedInText;

    /**
     * EmbeddedFont constructor.
     */
    public function __construct(string $fontData, \PdfGenerator\Font\IR\Structure\Font $font, string $charactersUsedInText)
    {
        $this->fontData = $fontData;
        $this->font = $font;
        $this->charactersUsedInText = $charactersUsedInText;
    }

    public function getFontData(): string
    {
        return $this->fontData;
    }

    public function getFont(): \PdfGenerator\Font\IR\Structure\Font
    {
        return $this->font;
    }

    public function getCharactersUsedInText(): string
    {
        return $this->charactersUsedInText;
    }

    /**
     * @return mixed
     *
     * @throws \Exception
     */
    public function accept(DocumentVisitor $documentVisitor)
    {
        return $documentVisitor->visitEmbeddedFont($this);
    }
}
