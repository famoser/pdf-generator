<?php

namespace Famoser\PdfGenerator\Font\Backend\File\Table\Base;

use Famoser\PdfGenerator\Font\Backend\File\TableVisitor;

abstract class BaseTable
{
    abstract public function accept(TableVisitor $visitor): string;
}
