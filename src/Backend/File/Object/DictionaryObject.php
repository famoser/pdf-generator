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
use PdfGenerator\Backend\File\Token\NumberToken;
use PdfGenerator\Backend\File\Token\ReferenceToken;
use PdfGenerator\Backend\File\Token\TextToken;

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
        $this->dictionaryToken->setEntry($key, new ReferenceToken($object));
    }

    public function addTextEntry(string $key, string $text)
    {
        $this->dictionaryToken->setTextEntry($key, $text);
    }

    /**
     * @param float|int $number
     */
    public function addNumberEntry(string $key, $number)
    {
        $this->dictionaryToken->setEntry($key, new NumberToken($number));
    }

    public function addDictionaryEntry(string $key, DictionaryToken $dictionaryToken)
    {
        $this->dictionaryToken->setEntry($key, $dictionaryToken);
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

        $this->dictionaryToken->setEntry($key, new ArrayToken($tokens));
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

        $this->dictionaryToken->setEntry($key, new ArrayToken($tokens));
    }

    /**
     * @param int[] $numbers
     */
    public function addTextArrayEntry(string $key, array $numbers, string $prefix = '')
    {
        $tokens = [];

        foreach ($numbers as $number) {
            $tokens[] = new TextToken($prefix . $number);
        }

        $this->dictionaryToken->setEntry($key, new ArrayToken($tokens));
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

        $this->dictionaryToken->setEntry($key, new ArrayToken($tokens));
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
        // target: (D:20191225080419-00'00)
        // target: (D:2019 12 25 08 04 19-00'00)
        $text = $dateTime->format('YmdHisP');
        $text = str_replace(':', "'", $text);
        $this->addTextEntry($key, '(D:' . $text . ')');
    }
}
