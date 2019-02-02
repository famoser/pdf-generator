<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\IR\Object\Token;

use Pdf\Backend\Object\TokenVisitor;

class TextToken extends BaseToken
{
    /**
     * @var string
     */
    private $text;

    /**
     * TextToken constructor.
     *
     * @param string $text
     */
    public function __construct(string  $text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param TokenVisitor $visitor
     */
    public function accept(TokenVisitor $visitor)
    {
        $visitor->visitTextToken($this);
    }
}
