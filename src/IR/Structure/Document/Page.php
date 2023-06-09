<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Document;

use PdfGenerator\IR\Structure\Document\Base\BaseDocumentStructure;
use PdfGenerator\IR\Structure\Document\Page\Content\Base\BaseContent;
use PdfGenerator\IR\Structure\DocumentVisitor;

class Page extends BaseDocumentStructure
{
    /**
     * @var float[]
     */
    private array $size;

    private string $pageNumber;

    /**
     * @var BaseContent[]
     */
    private array $content = [];

    /**
     * Page constructor.
     */
    public function __construct(string $pageNumber, array $size)
    {
        $this->pageNumber = $pageNumber;
        $this->size = $size;
    }

    public function addContent(BaseContent $baseContent): void
    {
        $this->content[] = $baseContent;
    }

    public function getIdentifier(): string
    {
        return $this->pageNumber;
    }

    /**
     * @return BaseContent[]
     */
    public function getContent(): array
    {
        return $this->content;
    }

    public function accept(DocumentVisitor $visitor): \PdfGenerator\Backend\Structure\Document\Page
    {
        return $visitor->visitPage($this);
    }

    public function getSize(): array
    {
        return $this->size;
    }
}
