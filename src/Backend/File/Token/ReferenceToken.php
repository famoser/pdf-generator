<?php

namespace Famoser\PdfGenerator\Backend\File\Token;

use Famoser\PdfGenerator\Backend\File\Object\Base\BaseObject;
use Famoser\PdfGenerator\Backend\File\Token\Base\BaseToken;
use Famoser\PdfGenerator\Backend\File\TokenVisitor;

class ReferenceToken extends BaseToken
{
    public function __construct(private readonly BaseObject $target)
    {
    }

    public function accept(TokenVisitor $visitor): string
    {
        return $visitor->visitReferenceToken($this);
    }

    public function getTarget(): BaseObject
    {
        return $this->target;
    }
}
