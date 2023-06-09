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
    private string $baseFont;

    /**
     * DefaultFont constructor.
     */
    public function __construct(string $baseFont)
    {
        $this->baseFont = $baseFont;
    }

    public function getBaseFont(): string
    {
        return $this->baseFont;
    }

    public function accept(DocumentVisitor $documentVisitor)
    {
        return $documentVisitor->visitDefaultFont($this);
    }
}
