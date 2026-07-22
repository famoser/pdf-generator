<?php

namespace Famoser\PdfGenerator\Backend\File\Object\Base;

use Famoser\PdfGenerator\Backend\File\ObjectVisitor;

abstract class BaseObject
{
    public function __construct(private readonly int $number)
    {
    }

    abstract public function accept(ObjectVisitor $visitor): string;

    public function getNumber(): int
    {
        return $this->number;
    }
}
