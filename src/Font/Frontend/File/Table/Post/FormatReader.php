<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\File\Table\Post;

use PdfGenerator\Font\Frontend\File\Table\Post\Format\Format;
use PdfGenerator\Font\Frontend\File\Table\Post\Format\Format1;
use PdfGenerator\Font\Frontend\File\Table\Post\Format\Format2;
use PdfGenerator\Font\Frontend\File\Table\Post\Format\Format25;
use PdfGenerator\Font\Frontend\File\Table\Post\Format\Format3;
use PdfGenerator\Font\Frontend\StreamReader;

class FormatReader
{
    public function readFormat(StreamReader $streamReader, float $format, int $length): Format
    {
        return match ($format) {
            1.0 => new Format1(),
            2.0 => $this->readFormat2($streamReader, $length),
            2.5 => $this->readFormat25($streamReader),
            3.0 => new Format3(),
            default => throw new \Exception('unknown post format '.$format),
        };
    }

    private function readFormat2(StreamReader $streamReader, int $length): Format2
    {
        $format2 = new Format2();

        $format2->setNumGlyphs($streamReader->readUInt16());
        $format2->setGlyphNameIndex($streamReader->readUInt16Array($format2->getNumGlyphs()));

        $formatLength = 2 + 2 * $format2->getNumGlyphs();
        $remainingLength = $length - $formatLength;
        $format2->setNames($streamReader->readFor($remainingLength));

        return $format2;
    }

    private function readFormat25(StreamReader $streamReader): Format25
    {
        $format = new Format25();

        $format->setNumGlyphs($streamReader->readUInt16());
        $format->setOffsets($streamReader->readInt8Array($format->getNumGlyphs()));

        return $format;
    }
}
