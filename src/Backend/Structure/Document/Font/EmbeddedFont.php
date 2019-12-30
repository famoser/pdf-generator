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
    const ENCODING_UTF_8 = 'UTF-8';

    /**
     * @var string
     */
    private $encoding;

    /**
     * @var string
     */
    private $fontPath;

    /**
     * @var string
     */
    private $charactersUsedInText;

    /**
     * EmbeddedFont constructor.
     */
    public function __construct(string $encoding, string $fontPath, string $charactersUsedInText)
    {
        $this->encoding = $encoding;
        $this->fontPath = $fontPath;
        $this->charactersUsedInText = $charactersUsedInText;
    }

    public function getEncoding(): string
    {
        return $this->encoding;
    }

    public function getFontPath(): string
    {
        return $this->fontPath;
    }

    public function getCharactersUsedInText(): string
    {
        return $this->charactersUsedInText;
    }

    /**
     * @throws \Exception
     *
     * @return mixed
     */
    public function accept(DocumentVisitor $documentVisitor)
    {
        return $documentVisitor->visitEmbeddedFont($this);
    }
}
