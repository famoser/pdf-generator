<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\Backend\Object;

use Pdf\Backend\Object\Base\BaseObject;
use Pdf\Backend\ObjectVisitor;
use Pdf\Backend\Token\ArrayToken;
use Pdf\Backend\Token\DictionaryToken;
use Pdf\Backend\Token\NumberToken;
use Pdf\Backend\Token\ReferenceToken;
use Pdf\Backend\Token\TextToken;

class DictionaryObject extends BaseObject
{
    /**
     * @var DictionaryToken
     */
    private $dictionaryToken;

    /**
     * @param string $key
     * @param BaseObject $object
     */
    public function addReferenceEntry(string $key, BaseObject $object)
    {
        $this->dictionaryToken->setEntry($key, new ReferenceToken($object));
    }

    /**
     * @param string $key
     * @param string $text
     */
    public function addTextEntry(string $key, string $text)
    {
        $this->dictionaryToken->setEntry($key, new TextToken($text));
    }

    /**
     * @param string $key
     * @param float|int $number
     */
    public function addNumberEntry(string $key, $number)
    {
        $this->dictionaryToken->setEntry($key, new NumberToken($number));
    }

    /**
     * @param string $key
     * @param int[] $numbers
     */
    public function addNumberArrayEntry(string $key, array $numbers)
    {
        $tokens = [];

        foreach ($numbers as $number) {
            $tokens[] = new NumberToken($number);
        }

        $this->dictionaryToken->setEntry($key, new ArrayToken($tokens));
    }

    /**
     * @param string $key
     * @param BaseObject[] $references
     */
    public function addReferenceArrayEntry(string $key, array $references)
    {
        $tokens = [];

        foreach ($references as $reference) {
            $tokens[] = new ReferenceToken($reference);
        }

        $this->dictionaryToken->setEntry($key, new ArrayToken($tokens));
    }

    /**
     * @param ObjectVisitor $visitor
     *
     * @return string
     */
    public function accept(ObjectVisitor $visitor): string
    {
        return $visitor->visitDictionary($this);
    }

    /**
     * @return DictionaryToken
     */
    public function getDictionaryToken(): DictionaryToken
    {
        return $this->dictionaryToken;
    }
}
