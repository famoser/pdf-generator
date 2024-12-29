<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\File\Token;

use Famoser\PdfGenerator\Backend\File\Object\Base\BaseObject;
use Famoser\PdfGenerator\Backend\File\Token\Base\BaseToken;
use Famoser\PdfGenerator\Backend\File\TokenVisitor;

class DictionaryToken extends BaseToken
{
    /**
     * @var BaseToken[]
     */
    private array $keyValue = [];

    /**
     * @param BaseToken[] $tokens
     */
    public function setArrayEntry(string $key, array $tokens): void
    {
        $this->setEntry($key, new ArrayToken($tokens));
    }

    public function setBooleanEntry(string $key, bool $value): void
    {
        $this->setEntry($key, new BooleanToken($value));
    }

    public function setTextEntry(string $key, string $value): void
    {
        $this->setEntry($key, new TextToken($value));
    }

    public function setNameEntry(string $key, string $value): void
    {
        $this->setEntry($key, new NameToken($value));
    }

    public function setReferenceEntry(string $key, BaseObject $value): void
    {
        $this->setEntry($key, new ReferenceToken($value));
    }

    public function setDictionaryEntry(string $key, self $token): void
    {
        $this->setEntry($key, $token);
    }

    public function setNumberEntry(string $key, float|int $value): void
    {
        $this->setEntry($key, new NumberToken($value));
    }

    private function setEntry(string $key, BaseToken $token): void
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
