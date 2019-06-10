<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure2\Base;

use PdfGenerator\IR\Structure2Visitor;

abstract class BaseStructure2
{
    /**
     * @param Structure2Visitor $visitor
     *
     * @return mixed
     */
    abstract public function accept(Structure2Visitor $visitor);

    /**
     * @return mixed
     */
    abstract public function getIdentifier();
}
