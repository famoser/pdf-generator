<?php

namespace Famoser\PdfGenerator\Backend\Structure\Document;

use Famoser\PdfGenerator\Backend\Structure\Document\Base\BaseDocumentStructure;
use Famoser\PdfGenerator\Backend\Structure\Document\Xmp\DublinCoreElements;
use Famoser\PdfGenerator\Backend\Structure\Document\Xmp\Pdf;
use Famoser\PdfGenerator\Backend\Structure\DocumentVisitor;

readonly class XmpMeta extends BaseDocumentStructure
{
    public function __construct(private Pdf $pdf, private DublinCoreElements $coreElements)
    {
    }

    public static function createEmpty(): self
    {
        return new self(Pdf::createEmpty(), DublinCoreElements::createEmpty());
    }

    public function getPdf(): Pdf
    {
        return $this->pdf;
    }

    public function getCoreElements(): DublinCoreElements
    {
        return $this->coreElements;
    }

    public function accept(DocumentVisitor $documentVisitor): mixed
    {
        return $documentVisitor->visitXmpMeta($this);
    }
}
