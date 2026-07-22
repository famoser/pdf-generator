<?php

namespace Famoser\PdfGenerator\Backend\File\Token;

use Famoser\PdfGenerator\Backend\File\Token\Base\BaseToken;
use Famoser\PdfGenerator\Backend\File\TokenVisitor;

class NumberToken extends BaseToken
{
    public function __construct(private readonly float|int $number)
    {
    }

    public static function format(float|int $number): float
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
