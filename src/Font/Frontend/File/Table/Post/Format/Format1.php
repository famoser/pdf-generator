<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\File\Table\Post\Format;

use PdfGenerator\Font\Frontend\File\Table\Post\VisitorInterface;

/**
 * this format uses the standard macintosh ordering; hence all indexes are predefined.
 */
class Format1 extends Format
{
    /**
     * @param VisitorInterface $visitor
     *
     * @return mixed
     */
    public function accept(VisitorInterface $visitor)
    {
        return $visitor->visitFormat1($this);
    }
}
