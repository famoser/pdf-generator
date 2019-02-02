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

class ArrayToken extends BaseToken
{
    /**
     * @var BaseToken[]
     */
    private $values;

    /**
     * ArrayToken constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * @param TokenVisitor $visitor
     */
    public function accept(TokenVisitor $visitor)
    {
        // TODO: Implement accept() method.
    }
}
