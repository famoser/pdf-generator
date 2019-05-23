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
    /**
     * @param StreamReader $streamReader
     * @param float $format
     * @param int $length
     *
     * @throws \Exception
     *
     * @return Format
     */
    public function readFormat(StreamReader $streamReader, float $format, int $length)
    {
        switch ($format) {
            case 1.0:
                return new Format1();
            case 2.0:
                return $this->readFormat2($streamReader, $length);
            case 2.5:
                return $this->readFormat25($streamReader);
            case 3:
                return new Format3();
            default:
                throw new \Exception('unknown post format ' . $format);
        }
    }

    /**
     * @param StreamReader $streamReader
     * @param int $length
     *
     * @throws \Exception
     *
     * @return Format2
     */
    private function readFormat2(StreamReader $streamReader, int $length)
    {
        $format2 = new Format2();

        $format2->setNumGlyphs($streamReader->readUInt16());
        $format2->setGlyphNameIndex($streamReader->readUInt16Array($format2->getNumGlyphs()));

        $formatLength = 2 + 2 * $format2->getNumGlyphs();
        $remainingLength = $length - $formatLength;
        $format2->setNames($streamReader->readFor($remainingLength));

        return $format2;
    }

    /**
     * @param StreamReader $streamReader
     *
     * @throws \Exception
     *
     * @return Format25
     */
    private function readFormat25(StreamReader $streamReader)
    {
        $format = new Format25();

        $format->setNumGlyphs($streamReader->readUInt16());
        $format->setOffsets($streamReader->readInt8Array($format->getNumGlyphs()));

        return $format;
    }
}
