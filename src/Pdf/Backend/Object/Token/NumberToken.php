<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\Backend\Object\Token;

use Pdf\Backend\Object\Token\Base\BaseToken;
use Pdf\Backend\TokenVisitor;

class NumberToken extends BaseToken
{
    /**
     * @var float|int
     */
    private $number;

    /**
     * TextToken constructor.
     *
     * @param float|int $number
     */
    public function __construct($number)
    {
        $this->number = $number;
    }

    /**
     * @param TokenVisitor $visitor
     *
     * @return string
     */
    public function accept(TokenVisitor $visitor): string
    {
        return $visitor->visitNumberToken($this);
    }

    /**
     * @return float|int
     */
    public function getNumber()
    {
        return $this->number;
    }
}
