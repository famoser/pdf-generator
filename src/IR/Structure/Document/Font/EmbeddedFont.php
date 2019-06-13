<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Font;

use PdfGenerator\IR\DocumentVisitor;
use PdfGenerator\IR\Structure\Font;

class EmbeddedFont extends Font
{
    /**
     * @var string
     */
    private $fontPath;

    /**
     * EmbeddedFont constructor.
     *
     * @param string $fontPath
     */
    public function __construct(string $fontPath)
    {
        $this->fontPath = $fontPath;
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
     * @return string
     */
    public function getFontPath(): string
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
}
