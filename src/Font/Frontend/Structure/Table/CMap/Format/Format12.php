<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\Structure\Table\CMap\Format;

use PdfGenerator\Font\Frontend\Structure\Table\CMap\VisitorInterface;

class Format12 extends Format
{
    /**
     * the format of the encoding.
     *
     * @ttf-type uint16
     *
     * @return int
     */
    public function getFormat(): int
    {
        return self::FORMAT_12;
    }

    /**
     * @param VisitorInterface $formatVisitor
     *
     * @return mixed
     */
    public function accept(VisitorInterface $formatVisitor)
    {
        return $formatVisitor->visitFormat12($this);
    }
}
