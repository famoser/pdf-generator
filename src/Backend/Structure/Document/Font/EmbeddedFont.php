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

use PdfGenerator\Backend\Catalog\Font\TrueType;
use PdfGenerator\Backend\Catalog\Font\Type0;
use PdfGenerator\Backend\Structure\Document\Font;
use PdfGenerator\Backend\Structure\DocumentVisitor;

readonly class EmbeddedFont extends Font
{
    public function __construct(private string $fontData, private \PdfGenerator\Font\IR\Structure\Font $font, private string $charactersUsedInText)
    {
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
     * @throws \Exception
     */
    public function accept(DocumentVisitor $documentVisitor): TrueType|Type0
    {
        return $documentVisitor->visitEmbeddedFont($this);
    }
}
