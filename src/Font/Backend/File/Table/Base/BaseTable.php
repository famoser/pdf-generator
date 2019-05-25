<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Backend\File\Table\Base;

use PdfGenerator\Font\Backend\File\TableVisitor;

abstract class BaseTable
{
    /**
     * @param TableVisitor $visitor
     *
     * @return string
     */
    abstract public function accept(TableVisitor $visitor): string;
}
