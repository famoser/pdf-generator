<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\IR\Object;

use Pdf\Backend\ObjectVisitor;
use Pdf\IR\Object\Token\BaseToken;
use Pdf\IR\Object\Token\ReferenceToken;
use Pdf\IR\Object\Token\TextToken;

class DictionaryObject extends BaseObject
{
    /**
     * @var BaseToken[]
     */
    private $keyValue;

    /**
     * @param string $key
     * @param BaseObject $object
     */
    public function addReferenceEntry(string $key, BaseObject $object)
    {
        $this->keyValue[$key] = new ReferenceToken($object);
    }

    /**
     * @param string $key
     * @param string $text
     */
    public function addTextEntry(string $key, string $text)
    {
        $this->keyValue[$key] = new TextToken($text);
    }

    /**
     * @param string $key
     * @param BaseObject $object
     */
    public function addArrayEntry(string $key, BaseObject $object)
    {
        $this->keyValue[$key] = new ReferenceToken($object);
    }

    /**
     * @param ObjectVisitor $visitor
     */
    public function accept(ObjectVisitor $visitor)
    {
        $visitor->visitDictionary($this);
    }
}
