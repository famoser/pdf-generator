<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
