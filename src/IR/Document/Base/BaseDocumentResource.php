<?php

namespace Famoser\PdfGenerator\IR\Document\Base;

use Famoser\PdfGenerator\Backend\Structure\Document\Base\BaseDocumentStructure;
use Famoser\PdfGenerator\IR\DocumentVisitor;

abstract readonly class BaseDocumentResource
{
    abstract public function accept(DocumentVisitor $visitor): BaseDocumentStructure;

    abstract public function getIdentifier(): string;
}
