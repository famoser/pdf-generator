<?php

namespace Famoser\PdfGenerator\Backend\Structure\Document\Base;

use Famoser\PdfGenerator\Backend\Structure\DocumentVisitor;

abstract readonly class BaseDocumentStructure
{
    abstract public function accept(DocumentVisitor $documentVisitor): mixed;
}
