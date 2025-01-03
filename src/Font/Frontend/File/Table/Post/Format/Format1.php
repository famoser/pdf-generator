<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Font\Frontend\File\Table\Post\Format;

use Famoser\PdfGenerator\Font\Frontend\File\Table\Post\FormatVisitorInterface;

/**
 * this format uses the standard macintosh ordering; hence all indexes are predefined.
 */
class Format1 extends Format
{
    public function accept(FormatVisitorInterface $visitor)
    {
        return $visitor->visitFormat1($this);
    }
}
