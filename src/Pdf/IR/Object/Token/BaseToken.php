<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\IR\Object\Token;

use Pdf\Backend\Object\TokenVisitor;

abstract class BaseToken
{
    /**
     * @param TokenVisitor $visitor
     */
    abstract public function accept(TokenVisitor $visitor);
}
