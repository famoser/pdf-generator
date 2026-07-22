<?php

namespace Famoser\PdfGenerator\IR\Document\Content\Base;

use Famoser\PdfGenerator\IR\Document\Content\ContentVisitorInterface;

abstract readonly class BaseContent
{
    /**
     * @template T
     *
     * @param ContentVisitorInterface<T> $visitor
     *
     * @return T
     */
    abstract public function accept(ContentVisitorInterface $visitor);
}
