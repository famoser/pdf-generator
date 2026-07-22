<?php

namespace Famoser\PdfGenerator\Backend\Structure\Document;

use Famoser\PdfGenerator\Backend\Structure\Document\Base\BaseDocumentStructure;
use Famoser\PdfGenerator\Backend\Structure\DocumentVisitor;

readonly class Info extends BaseDocumentStructure
{
    public function __construct(private ?string $lang, private ?string $title, private ?string $author, private ?string $subject, private ?string $keywords)
    {
    }

    public function getLang(): ?string
    {
        return $this->lang;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    /**
     * @return array<string, string>
     */
    public function accept(DocumentVisitor $documentVisitor): array
    {
        return $documentVisitor->visitInfo($this);
    }
}
