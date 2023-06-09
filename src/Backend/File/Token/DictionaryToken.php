<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\File\Token;

use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\File\Token\Base\BaseToken;
use PdfGenerator\Backend\File\TokenVisitor;

class DictionaryToken extends BaseToken
{
    /**
     * @var BaseToken[]
     */
    private array $keyValue;

    /**
     * @param BaseToken[] $tokens
     */
    public function setArrayEntry(string $key, array $tokens)
    {
        $this->setEntry($key, new ArrayToken($tokens));
    }

    public function setTextEntry(string $key, string $value)
    {
        $this->setEntry($key, new TextToken($value));
    }

    public function setNameEntry(string $key, string $value)
    {
        $this->setEntry($key, new NameToken($value));
    }

    public function setReferenceEntry(string $key, BaseObject $value)
    {
        $this->setEntry($key, new ReferenceToken($value));
    }

    public function setDictionaryEntry(string $key, self $token)
    {
        $this->setEntry($key, $token);
    }

    /**
     * @param float|int $value
     */
    public function setNumberEntry(string $key, $value)
    {
        $this->setEntry($key, new NumberToken($value));
    }

    private function setEntry(string $key, BaseToken $token)
    {
        $this->keyValue[$key] = $token;
    }

    public function accept(TokenVisitor $visitor): string
    {
        return $visitor->visitDictionaryToken($this);
    }

    /**
     * @return BaseToken[]
     */
    public function getKeyValue(): array
    {
        return $this->keyValue;
    }
}
