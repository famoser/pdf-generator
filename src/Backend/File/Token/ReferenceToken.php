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

class ReferenceToken extends BaseToken
{
    /**
     * @var BaseObject
     */
    private $target;

    /**
     * ReferenceEntry constructor.
     */
    public function __construct(BaseObject $target)
    {
        $this->target = $target;
    }

    public function accept(TokenVisitor $visitor): string
    {
        return $visitor->visitReferenceToken($this);
    }

    public function getTarget(): BaseObject
    {
        return $this->target;
    }
}
