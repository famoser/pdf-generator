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

class DefaultFont extends Font
{
    /**
     * @var string
     */
    private $baseFont;

    /**
     * equivalent to Windows-1252 according to comment https://www.php.net/manual/de/haru.builtin.encodings.php.
     */
    const ENCODING_WIN_ANSI_ENCODING = 'WinAnsiEncoding';

    /**
     * @var string
     */
    private $encoding;

    /**
     * DefaultFont constructor.
     */
    public function __construct(string $baseFont, string $encoding)
    {
        $this->baseFont = $baseFont;
        $this->encoding = $encoding;
    }

    public function getBaseFont(): string
    {
        return $this->baseFont;
    }

    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * @return mixed
     */
    public function accept(DocumentVisitor $documentVisitor)
    {
        return $documentVisitor->visitDefaultFont($this);
    }
}
