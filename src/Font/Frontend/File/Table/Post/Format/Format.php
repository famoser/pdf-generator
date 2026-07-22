<?php

namespace Famoser\PdfGenerator\Font\Frontend\File\Table\Post\Format;

use Famoser\PdfGenerator\Font\Frontend\File\Table\Post\FormatVisitorInterface;

abstract class Format
{
    /**
     * @template T
     *
     * @param FormatVisitorInterface<T> $visitor
     *
     * @return T
     */
    abstract public function accept(FormatVisitorInterface $visitor);
}
