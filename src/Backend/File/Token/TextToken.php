<?php

namespace Famoser\PdfGenerator\Backend\File\Token;

use Famoser\PdfGenerator\Backend\File\Token\Base\BaseToken;
use Famoser\PdfGenerator\Backend\File\TokenVisitor;

class TextToken extends BaseToken
{
    public function __construct(private readonly string $text)
    {
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function accept(TokenVisitor $visitor): string
    {
        return $visitor->visitTextToken($this);
    }
}
