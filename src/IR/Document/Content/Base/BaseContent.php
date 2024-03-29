<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Document\Content\Base;

use PdfGenerator\IR\Document\Content\ContentVisitorInterface;

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
