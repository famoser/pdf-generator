<?php

namespace Famoser\PdfGenerator\Backend\File\Token;

use Famoser\PdfGenerator\Backend\File\Token\Base\BaseToken;
use Famoser\PdfGenerator\Backend\File\TokenVisitor;

class ArrayToken extends BaseToken
{
    /**
     * @param BaseToken[] $values
     */
    public function __construct(private readonly array $values, private readonly ?BaseToken $key = null)
    {
    }

    public function accept(TokenVisitor $visitor): string
    {
        return $visitor->visitArrayToken($this);
    }

    public function getKey(): ?BaseToken
    {
        return $this->key;
    }

    /**
     * @return BaseToken[]
     */
    public function getValues(): array
    {
        return $this->values;
    }
}
