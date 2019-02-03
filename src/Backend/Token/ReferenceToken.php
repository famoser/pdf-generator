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

use PdfGenerator\Backend\Object\Base\BaseObject;
use PdfGenerator\Backend\Token\Base\BaseToken;
use PdfGenerator\Backend\TokenVisitor;

class ReferenceToken extends BaseToken
{
    /**
     * @var BaseObject
     */
    private $target;

    /**
     * ReferenceEntry constructor.
     *
     * @param BaseObject $target
     */
    public function __construct(BaseObject $target)
    {
        $this->target = $target;
    }

    /**
     * @param TokenVisitor $visitor
     *
     * @return string
     */
    public function accept(TokenVisitor $visitor): string
    {
        return $visitor->visitReferenceToken($this);
    }

    /**
     * @return BaseObject
     */
    public function getTarget(): BaseObject
    {
        return $this->target;
    }
}
