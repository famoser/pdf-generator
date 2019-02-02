<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\Backend;

class Printer
{
    /**
     * @var int
     */
    private $byteCount;

    /**
     * @param DictionaryObject $dictionaryObject
     */
    public function printDictionary(DictionaryObject $dictionaryObject)
    {
    }
}
