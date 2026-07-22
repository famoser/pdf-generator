<?php

namespace Famoser\PdfGenerator\Backend\File\Structure\Base;

use Famoser\PdfGenerator\Backend\File\StructureVisitor;

abstract class BaseStructure
{
    abstract public function accept(StructureVisitor $visitor): string;
}
