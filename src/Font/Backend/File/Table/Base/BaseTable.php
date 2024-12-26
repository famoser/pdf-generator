<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Font\Backend\File\Table\Base;

use Famoser\PdfGenerator\Font\Backend\File\TableVisitor;

abstract class BaseTable
{
    abstract public function accept(TableVisitor $visitor): string;
}
