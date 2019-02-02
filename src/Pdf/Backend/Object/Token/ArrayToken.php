<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\Backend\Object\Token;

use Pdf\Backend\Object\Token\Base\BaseToken;
use Pdf\Backend\TokenVisitor;

class ArrayToken extends BaseToken
{
    /**
     * @var BaseToken[]
     */
    private $values;

    /**
     * ArrayToken constructor.
     *
     * @param BaseToken[] $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * @param TokenVisitor $visitor
     *
     * @return string
     */
    public function accept(TokenVisitor $visitor): string
    {
        return $visitor->visitArrayToken($this);
    }

    /**
     * @return BaseToken[]
     */
    public function getValues(): array
    {
        return $this->values;
    }
}
