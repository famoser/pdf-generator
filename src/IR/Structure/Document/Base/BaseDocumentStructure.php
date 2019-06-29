<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Document\Base;

use PdfGenerator\IR\Structure\DocumentVisitor;

abstract class BaseDocumentStructure
{
    /**
     * @param DocumentVisitor $visitor
     *
     * @return mixed
     */
    abstract public function accept(DocumentVisitor $visitor);

    /**
     * @return string
     */
    abstract public function getIdentifier();
}
