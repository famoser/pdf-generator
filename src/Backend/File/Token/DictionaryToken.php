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

use PdfGenerator\Backend\File\Token\Base\BaseToken;
use PdfGenerator\Backend\File\TokenVisitor;

class DictionaryToken extends BaseToken
{
    /**
     * @var BaseToken[]
     */
    private $keyValue;

    /**
     * @param string $key
     * @param BaseToken $token
     */
    public function setEntry(string $key, BaseToken $token)
    {
        $this->keyValue[$key] = $token;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function setTextEntry(string $key, string $value)
    {
        $this->keyValue[$key] = new TextToken($value);
    }

    /**
     * @param string $key
     * @param float|int $value
     */
    public function setNumberEntry(string $key, $value)
    {
        $this->keyValue[$key] = new NumberToken($value);
    }

    /**
     * @param TokenVisitor $visitor
     *
     * @return string
     */
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
