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

class ObjectVisitor
{
    /**
     * @param \Pdf\IR\Object\DictionaryObject $dictionary
     */
    public function visitDictionary(\Pdf\IR\Object\DictionaryObject $dictionary)
    {
        $start = $dictionary->getNumber() . ' 0 obj <<';
        foreach ($dictionary->getEntries() as $entry) {
        }
    }
}
