<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\IR;

use Pdf\IR\Object\BaseObject;

class Document
{
    /**
     * @var BaseObject[]
     */
    private $entries = [];

    /**
     * @param BaseObject $baseObject
     */
    public function addEntry(BaseObject $baseObject)
    {
        $this->entries[] = $baseObject;
    }
}
