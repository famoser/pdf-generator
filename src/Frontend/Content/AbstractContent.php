<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Content;

use PdfGenerator\Frontend\LayoutEngine\ContentVisitorInterface;
use PdfGenerator\Frontend\Printer;

abstract class AbstractContent
{
    /**
     * @template T
     *
     * @param ContentVisitorInterface<T> $visitor
     *
     * @return T
     */
    abstract public function accept(ContentVisitorInterface $visitor);

    abstract public function print(Printer $printer, float $width, float $height): void;
}
