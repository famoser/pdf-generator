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
    private $fontContent;

    /**
     * EmbeddedFont constructor.
     *
     * @param string $encoding
     * @param string $fontContent
     */
    public function __construct(string $encoding, string $fontContent)
    {
        $this->encoding = $encoding;
        $this->fontContent = $fontContent;
    }

    /**
     * @return string
     */
    public function getFontContent(): string
    {
        return $this->fontContent;
    }

    /**
     * @param DocumentVisitor $documentVisitor
     *
     * @return mixed
     */
    public function accept(DocumentVisitor $documentVisitor)
    {
        return $documentVisitor->visitEmbeddedFont($this);
    }
}
