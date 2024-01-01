<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Document\Base;

use PdfGenerator\Backend\Structure\Document\Base\BaseDocumentStructure;
use PdfGenerator\IR\DocumentVisitor;

abstract readonly class BaseDocumentResource
{
    abstract public function accept(DocumentVisitor $visitor): BaseDocumentStructure;

    abstract public function getIdentifier(): string;
}
