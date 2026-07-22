<?php

namespace Famoser\PdfGenerator\IR\Document\Resource;

use Famoser\PdfGenerator\IR\Document\Base\BaseDocumentResource;
use Famoser\PdfGenerator\IR\Document\Resource\Font\FontVisitor;

abstract readonly class Font extends BaseDocumentResource
{
    /**
     * @template T
     *
     * @param FontVisitor<T> $visitor
     *
     * @return T
     */
    abstract public function acceptFont(FontVisitor $visitor);

    abstract public function getUnitsPerEm(): int;

    abstract public function getAscender(): int;

    abstract public function getDescender(): int;

    abstract public function getLineGap(): int;

    public function getBaselineToBaselineDistance(): int
    {
        return $this->getAscender() - $this->getDescender() + $this->getLineGap();
    }
}
