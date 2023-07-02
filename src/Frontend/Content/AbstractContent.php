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

use PdfGenerator\Frontend\LayoutEngine\AbstractContentVisitor;
use PdfGenerator\Frontend\Printer;

abstract class AbstractContent
{
    abstract public function accept(AbstractContentVisitor $visitor): mixed;

    abstract public function print(Printer $printer, float $width, float $height): void;
}
