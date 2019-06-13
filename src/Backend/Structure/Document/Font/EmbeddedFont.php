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
    private $content;

    /**
     * @var string
     */
    private $usedWithText;

    /**
     * EmbeddedFont constructor.
     *
     * @param string $encoding
     * @param string $content
     * @param string $usedWithText
     */
    public function __construct(string $encoding, string $content, string $usedWithText)
    {
        $this->encoding = $encoding;
        $this->content = $content;
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
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
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
     * @return mixed
     */
    public function accept(DocumentVisitor $documentVisitor)
    {
        return $documentVisitor->visitEmbeddedFont($this);
    }
}
