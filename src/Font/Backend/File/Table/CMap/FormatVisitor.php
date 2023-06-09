<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Backend\File\Table\CMap;

use PdfGenerator\Font\Backend\File\Traits\Writer;
use PdfGenerator\Font\Backend\StreamWriter;

class FormatVisitor
{
    public function visitFormat4(Format\Format4 $format, StreamWriter $writer): void
    {
        $writer->writeUInt16(4);
        $writer->writeUInt16($format->getLength());
        $writer->writeUInt16($format->getLanguage());

        $writer->writeUInt16($format->getSegCountX2());
        Writer::writeBinaryTreeSearchableUInt16($format, $writer);

        $writer->writeUInt16Array($format->getEndCodes());
        $writer->writeUInt16($format->getReservedPad());
        $writer->writeUInt16Array($format->getStartCodes());
        $writer->writeInt16Array($format->getIdDeltas());
        $writer->writeUInt16Array($format->getIdRangeOffsets());
        $writer->writeUInt16Array($format->getGlyphIndexArray());
    }
}
