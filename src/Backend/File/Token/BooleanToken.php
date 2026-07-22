<?php

namespace Famoser\PdfGenerator\Backend\File\Token;

use Famoser\PdfGenerator\Backend\File\Token\Base\BaseToken;
use Famoser\PdfGenerator\Backend\File\TokenVisitor;

class BooleanToken extends BaseToken
{
    public function __construct(private readonly bool $value)
    {
    }

    public function getValue(): bool
    {
        return $this->value;
    }

    public function accept(TokenVisitor $visitor): string
    {
        return $visitor->visitBoolToken($this);
    }
}
