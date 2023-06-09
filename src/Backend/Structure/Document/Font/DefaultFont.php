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

readonly class DefaultFont extends Font
{
    /**
     * DefaultFont constructor.
     */
    public function __construct(private string $baseFont)
    {
    }

    public function getBaseFont(): string
    {
        return $this->baseFont;
    }

    public function accept(DocumentVisitor $documentVisitor): \PdfGenerator\Backend\Catalog\Font\Type1
    {
        return $documentVisitor->visitDefaultFont($this);
    }
}
