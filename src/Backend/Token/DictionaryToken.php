<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Token;

use PdfGenerator\Backend\Token\Base\BaseToken;
use PdfGenerator\Backend\TokenVisitor;

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
