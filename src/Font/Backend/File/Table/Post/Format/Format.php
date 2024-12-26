<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Font\Backend\File\Table\Post\Format;

use Famoser\PdfGenerator\Font\Backend\File\Table\Post\FormatVisitor;
use Famoser\PdfGenerator\Font\Backend\StreamWriter;

abstract class Format
{
    abstract public function accept(FormatVisitor $formatVisitor, StreamWriter $streamWriter): void;
}
