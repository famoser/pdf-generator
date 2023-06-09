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

class ArrayToken extends BaseToken
{
    private ?BaseToken $key;

    /**
     * @var BaseToken[]
     */
    private array $values;

    /**
     * ArrayToken constructor.
     *
     * @param BaseToken[] $values
     */
    public function __construct(array $values, BaseToken $key = null)
    {
        $this->key = $key;
        $this->values = $values;
    }

    public function accept(TokenVisitor $visitor): string
    {
        return $visitor->visitArrayToken($this);
    }

    public function getKey(): ?BaseToken
    {
        return $this->key;
    }

    /**
     * @return BaseToken[]
     */
    public function getValues(): array
    {
        return $this->values;
    }
}
