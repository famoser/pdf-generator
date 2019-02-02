<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\Backend\Object;

class TokenVisitor
{
    /**
     * @param \Pdf\IR\Object\Token\ReferenceToken $referenceEntry
     */
    public function visitReferenceEntry(\Pdf\IR\Object\Token\ReferenceToken $referenceEntry)
    {
    }
}
