<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\File\Object;

use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\File\ObjectVisitor;
use PdfGenerator\Backend\File\Token\ArrayToken;
use PdfGenerator\Backend\File\Token\DictionaryToken;
use PdfGenerator\Backend\File\Token\NameToken;
use PdfGenerator\Backend\File\Token\NumberToken;
use PdfGenerator\Backend\File\Token\ReferenceToken;

class DictionaryObject extends BaseObject
{
    /**
     * @var DictionaryToken
     */
    private $dictionaryToken;

    public function __construct(int $number)
    {
        parent::__construct($number);

        $this->dictionaryToken = new DictionaryToken();
    }

    public function addReferenceEntry(string $key, BaseObject $object)
    {
        $this->dictionaryToken->setReferenceEntry($key, $object);
    }

    public function addTextEntry(string $key, string $text)
    {
        $this->dictionaryToken->setTextEntry($key, $text);
    }

    public function addNameEntry(string $key, string $name)
    {
        $this->dictionaryToken->setNameEntry($key, $name);
    }

    /**
     * @param float|int $number
     */
    public function addNumberEntry(string $key, $number)
    {
        $this->dictionaryToken->setNumberEntry($key, $number);
    }

    public function addDictionaryEntry(string $key, DictionaryToken $dictionaryToken)
    {
        $this->dictionaryToken->setDictionaryEntry($key, $dictionaryToken);
    }

    /**
     * @param int[] $numbers
     */
    public function addNumberArrayEntry(string $key, array $numbers)
    {
        $tokens = [];

        foreach ($numbers as $number) {
            $tokens[] = new NumberToken($number);
        }

        $this->dictionaryToken->setArrayEntry($key, $tokens);
    }

    /**
     * @param int[] $numbers
     */
    public function addNumberOfNumbersArrayEntry(string $key, array $numberOfNumbers)
    {
        $tokens = [];
        foreach ($numberOfNumbers as $index => $numbers) {
            if (\is_array($numbers)) {
                $numberTokens = [];
                foreach ($numbers as $number) {
                    $numberTokens[] = new NumberToken($number);
                }

                $tokens[] = new ArrayToken($numberTokens, new NumberToken($index));
            } else {
                $tokens[] = new NumberToken($numbers);
            }
        }

        $this->dictionaryToken->setArrayEntry($key, $tokens);
    }

    /**
     * @param int[] $values
     */
    public function addNameArrayEntry(string $key, array $values)
    {
        $tokens = [];

        foreach ($values as $value) {
            $tokens[] = new NameToken($value);
        }

        $this->dictionaryToken->setArrayEntry($key, $tokens);
    }

    /**
     * @param BaseObject[] $references
     */
    public function addReferenceArrayEntry(string $key, array $references)
    {
        $tokens = [];

        foreach ($references as $reference) {
            $tokens[] = new ReferenceToken($reference);
        }

        $this->dictionaryToken->setArrayEntry($key, $tokens);
    }

    public function accept(ObjectVisitor $visitor): string
    {
        return $visitor->visitDictionary($this);
    }

    public function getDictionaryToken(): DictionaryToken
    {
        return $this->dictionaryToken;
    }

    public function addDateEntry(string $key, \DateTime $dateTime)
    {
        // target: D:20191225080419-00'00
        $text = $dateTime->format('YmdHisP');
        $text = str_replace(':', "'", $text);
        $this->addTextEntry($key, 'D:' . $text);
    }
}
