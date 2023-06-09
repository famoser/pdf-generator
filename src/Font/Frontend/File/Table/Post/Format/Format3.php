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
 * specifies that no PostScript information will be supplied
 * may break printers which relay on the PostScript information.
 */
class Format3 extends Format
{
    public function accept(VisitorInterface $visitor)
    {
        return $visitor->visitFormat3($this);
    }
}
