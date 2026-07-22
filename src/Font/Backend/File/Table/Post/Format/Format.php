<?php

namespace Famoser\PdfGenerator\Font\Backend\File\Table\Post\Format;

use Famoser\PdfGenerator\Font\Backend\File\Table\Post\FormatVisitor;
use Famoser\PdfGenerator\Font\Backend\StreamWriter;

abstract class Format
{
    abstract public function accept(FormatVisitor $formatVisitor, StreamWriter $streamWriter): void;
}
