<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\File\Token;

use PdfGenerator\Backend\File\Token\Base\BaseToken;
use PdfGenerator\Backend\File\TokenVisitor;

class NumberToken extends BaseToken
{
    private int|float $number;

    /**
     * TextToken constructor.
     */
    public function __construct(float|int $number)
    {
        $this->number = $number;
    }

    public static function format($number): float
    {
        return round($number, 6);
    }

    public function accept(TokenVisitor $visitor): string
    {
        return $visitor->visitNumberToken($this);
    }

    public function getNumber(): float|int
    {
        return $this->number;
    }
}
