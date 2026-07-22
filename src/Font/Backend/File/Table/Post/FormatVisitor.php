<?php

namespace Famoser\PdfGenerator\Font\Backend\File\Table\Post;

use Famoser\PdfGenerator\Font\Backend\StreamWriter;

class FormatVisitor
{
    public function visitFormat2(Format\Format2 $format2, StreamWriter $streamWriter): void
    {
        $streamWriter->writeUInt16($format2->getNumGlyphs());
        $streamWriter->writeUInt16Array($format2->getGlyphNameIndex());
        $streamWriter->writeStream($format2->getNames());
    }
}
