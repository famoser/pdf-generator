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

use PdfGenerator\IR\DocumentVisitor;
use PdfGenerator\IR\Structure\Document\Font;

class EmbeddedFont extends Font
{
    /**
     * @var string
     */
    private $fontPath;

    /**
     * @var \PdfGenerator\Font\IR\Structure\Font
     */
    private $font;

    /**
     * EmbeddedFont constructor.
     *
     * @param string $fontPath
     * @param \PdfGenerator\Font\IR\Structure\Font $font
     */
    public function __construct(string $fontPath, \PdfGenerator\Font\IR\Structure\Font $font)
    {
        $this->fontPath = $fontPath;
        $this->font = $font;
    }

    /**
     * @param DocumentVisitor $visitor
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function accept(DocumentVisitor $visitor)
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
     * sets the encoding used by the font.
     *
     * @param string $escaped
     *
     * @return string
     */
    public function encode(string $escaped): string
    {
        return mb_convert_encoding($escaped, 'UTF-8');
    }

    /**
     * @return \PdfGenerator\Font\IR\Structure\Font
     */
    public function getFont(): \PdfGenerator\Font\IR\Structure\Font
    {
        return $this->font;
    }
}
