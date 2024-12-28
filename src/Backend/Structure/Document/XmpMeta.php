<?php

namespace Famoser\PdfGenerator\Backend\Structure\Document;

use Famoser\PdfGenerator\Backend\Structure\Document\Base\BaseDocumentStructure;
use Famoser\PdfGenerator\Backend\Structure\Document\Xmp\DublinCoreElements;
use Famoser\PdfGenerator\Backend\Structure\DocumentVisitor;

readonly class XmpMeta extends BaseDocumentStructure
{
    public function __construct(private DublinCoreElements $coreElements)
    {
    }

    public static function createEmpty(): self
    {
        return new self(DublinCoreElements::createEmpty());
    }

    public function accept(DocumentVisitor $documentVisitor): mixed
    {
        return $documentVisitor->visitXmpMeta($this);
    }

    public function getCoreElements(): DublinCoreElements
    {
        return $this->coreElements;
    }
}
