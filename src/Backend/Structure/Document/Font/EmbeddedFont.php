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

use PdfGenerator\Backend\Structure\DocumentVisitor;
use PdfGenerator\Backend\Structure\Font;

class EmbeddedFont extends Font
{
    const ENCODING_UTF_8 = 'UTF-8';

    /**
     * @var string
     */
    private $encoding;

    /**
     * @var \PdfGenerator\Font\IR\Structure\Font
     */
    private $font;

    /**
     * @var string
     */
    private $usedWithText;

    /**
     * EmbeddedFont constructor.
     *
     * @param string $encoding
     * @param \PdfGenerator\Font\IR\Structure\Font $font
     * @param string $usedWithText
     */
    public function __construct(string $encoding, \PdfGenerator\Font\IR\Structure\Font $font, string $usedWithText)
    {
        $this->encoding = $encoding;
        $this->font = $font;
        $this->usedWithText = $usedWithText;
    }

    /**
     * @return string
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * @return \PdfGenerator\Font\IR\Structure\Font
     */
    public function getFont(): \PdfGenerator\Font\IR\Structure\Font
    {
        return $this->font;
    }

    /**
     * @return string
     */
    public function getUsedWithText(): string
    {
        return $this->usedWithText;
    }

    /**
     * @param DocumentVisitor $documentVisitor
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function accept(DocumentVisitor $documentVisitor)
    {
        return $documentVisitor->visitEmbeddedFont($this);
    }
}
