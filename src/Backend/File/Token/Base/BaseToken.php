<?php

namespace Famoser\PdfGenerator\Backend\File\Token\Base;

use Famoser\PdfGenerator\Backend\File\TokenVisitor;

abstract class BaseToken
{
    abstract public function accept(TokenVisitor $visitor): string;
}
