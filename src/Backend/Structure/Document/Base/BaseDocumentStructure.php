<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Document\Base;

use PdfGenerator\Backend\Structure\DocumentVisitor;

abstract class BaseDocumentStructure
{
    /**
     * @param DocumentVisitor $documentVisitor
     *
     * @return mixed
     */
    abstract public function accept(DocumentVisitor $documentVisitor);

    /**
     * @return string
     */
    abstract public function getIdentifier();
}
