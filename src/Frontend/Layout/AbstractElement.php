<?php

namespace Famoser\PdfGenerator\Frontend\Layout;

use Famoser\PdfGenerator\Frontend\Layout\Traits\ElementTrait;
use Famoser\PdfGenerator\Frontend\LayoutEngine\ElementVisitorInterface;

abstract class AbstractElement
{
    use ElementTrait;

    /**
     * @template T
     *
     * @param ElementVisitorInterface<T> $visitor
     *
     * @return T
     */
    abstract public function accept(ElementVisitorInterface $visitor);
}
