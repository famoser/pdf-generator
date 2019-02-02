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
use Pdf\IR\Object\BaseObject;

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
     */
    public function accept(TokenVisitor $visitor)
    {
        $visitor->visitReferenceEntry($this);
    }

    /**
     * @return BaseObject
     */
    public function getTarget(): BaseObject
    {
        return $this->target;
    }
}
