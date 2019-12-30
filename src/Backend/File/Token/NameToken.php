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

class NameToken extends BaseToken
{
    /**
     * @var string
     */
    private $name;

    /**
     * TextToken constructor.
     * all characters in $text must be between EXCLAMATION MARK(21h) (!) to TILDE (7Eh) (~)
     * else need to be escaped with # followed by its number; for example ( = #28.
     */
    public function __construct(string $text)
    {
        $this->name = $text;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function accept(TokenVisitor $visitor): string
    {
        return $visitor->visitNameToken($this);
    }
}
