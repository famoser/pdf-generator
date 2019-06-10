<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Font;

use PdfGenerator\Backend\Structure\DocumentVisitor;
use PdfGenerator\Backend\Structure\Font;

class DefaultFont extends Font
{
    /**
     * equivalent to Windows-1252 according to comment https://www.php.net/manual/de/haru.builtin.encodings.php.
     *
     * mb_convert_encoding($str, "Windows-1252", "UTF-8");
     */
    const ENCODING_WIN_ANSI_ENCODING = 'WinAnsiEncoding';

    /**
     * @var string
     */
    private $encoding;

    /**
     * DefaultFont constructor.
     *
     * @param string $baseFont
     * @param string $encoding
     */
    public function __construct(string $baseFont, string $encoding)
    {
        parent::__construct($baseFont);
        $this->encoding = $encoding;
    }

    /**
     * @return string
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * @param DocumentVisitor $documentVisitor
     *
     * @return mixed
     */
    public function accept(DocumentVisitor $documentVisitor)
    {
        return $documentVisitor->visitDefaultFont($this);
    }
}
