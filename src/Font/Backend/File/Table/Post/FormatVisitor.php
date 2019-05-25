<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Backend\File\Table\Post;

use PdfGenerator\Font\Backend\StreamWriter;

class FormatVisitor
{
    /**
     * @param Format\Format2 $format2
     * @param StreamWriter $streamWriter
     */
    public function visitFormat2(Format\Format2 $format2, StreamWriter $streamWriter)
    {
        $streamWriter->writeUInt16($format2->getNumGlyphs());
        $streamWriter->writeUInt16Array($format2->getGlyphNameIndex());
        $streamWriter->writeStream($format2->getNames());
    }
}
