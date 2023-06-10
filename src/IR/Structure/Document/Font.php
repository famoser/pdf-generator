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

use PdfGenerator\IR\Structure\Document\Base\BaseDocumentResource;
use PdfGenerator\IR\Structure\Document\Font\FontVisitor;

abstract readonly class Font extends BaseDocumentResource
{
    abstract public function accept(FontVisitor $visitor);

    abstract public function getUnitsPerEm();

    abstract public function getAscender();

    abstract public function getDescender();

    abstract public function getLineGap();

    public function getBaselineToBaselineDistance()
    {
        return $this->getAscender() - $this->getDescender() + $this->getLineGap();
    }
}
