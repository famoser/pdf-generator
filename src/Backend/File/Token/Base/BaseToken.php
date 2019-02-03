<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\File\Token\Base;

use PdfGenerator\Backend\File\TokenVisitor;

abstract class BaseToken
{
    /**
     * @param TokenVisitor $visitor
     *
     * @return string
     */
    abstract public function accept(TokenVisitor $visitor): string;
}
