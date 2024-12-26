<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Font\Frontend\File\Table\CMap;

use Famoser\PdfGenerator\Font\Frontend\File\Table\CMap\Format\Format;
use Famoser\PdfGenerator\Font\Frontend\File\Table\CMap\Format\Format0;
use Famoser\PdfGenerator\Font\Frontend\File\Table\CMap\Format\Format12;
use Famoser\PdfGenerator\Font\Frontend\File\Table\CMap\Format\Format12Group;
use Famoser\PdfGenerator\Font\Frontend\File\Table\CMap\Format\Format4;
use Famoser\PdfGenerator\Font\Frontend\File\Table\CMap\Format\Format6;
use Famoser\PdfGenerator\Font\Frontend\StreamReader;

class FormatReader
{
    public function readFormat(StreamReader $fileReader): ?Format
    {
        $startOffset = $fileReader->getOffset();
        $format = $fileReader->readUInt16();

        switch ($format) {
            case 0:
                return $this->readFormat0($fileReader);
            case 4:
                return $this->readFormat4($fileReader, $startOffset);
            case 6:
                return $this->readFormat6($fileReader);
        }

        $fileReader->setOffset($startOffset);
        $formatFixed = $fileReader->readFixed();

        return match ($formatFixed) {
            12.0 => $this->readFormat12($fileReader),
            default => null,
        };
    }

    private function readFormat0(StreamReader $fileReader): Format0
    {
        $format = new Format0();

        $this->readUInt16SharedFormat($fileReader, $format);

        $format->setGlyphIndexArray($fileReader->readUInt8Array(256));

        return $format;
    }

    private function readFormat4(StreamReader $fileReader, int $startOffset): Format4
    {
        $format = new Format4();

        $this->readUInt16SharedFormat($fileReader, $format);

        $format->setSegCountX2($fileReader->readUInt16());

        // read binary tree uint16
        $format->setSearchRange($fileReader->readUInt16());
        $format->setEntrySelector($fileReader->readUInt16());
        $format->setRangeShift($fileReader->readUInt16());

        $segCount = $format->getSegCountX2() / 2;
        $format->setEndCodes($fileReader->readUInt16Array($segCount));
        $format->setReservedPad($fileReader->readUInt16());
        $format->setStartCodes($fileReader->readUInt16Array($segCount));
        $format->setIdDeltas($fileReader->readInt16Array($segCount));
        $format->setIdRangeOffsets($fileReader->readUInt16Array($segCount));

        $tableEnd = $startOffset + $format->getLength();
        $glyphIndexes = ($tableEnd - $fileReader->getOffset()) / 2;
        $format->setGlyphIndexArray($fileReader->readUInt16Array($glyphIndexes));

        return $format;
    }

    private function readFormat6(StreamReader $fileReader): Format6
    {
        $format = new Format6();

        $this->readUInt16SharedFormat($fileReader, $format);

        $format->setFirstCode($fileReader->readUInt16());
        $format->setEntryCount($fileReader->readUInt16());
        $format->setGlyphIndexArray($fileReader->readUInt16Array($format->getEntryCount()));

        return $format;
    }

    private function readFormat12(StreamReader $fileReader): Format12
    {
        $format = new Format12();

        $format->setLength($fileReader->readUInt32());
        $format->setLanguage($fileReader->readUInt32());
        $format->setNGroups($fileReader->readUInt32());

        for ($i = 0; $i < count($format->getGroups()); ++$i) {
            $group = new Format12Group();

            $group->setStartCharCode($fileReader->readUInt32());
            $group->setEndCharCode($fileReader->readUInt32());
            $group->setStartGlyphCode($fileReader->readUInt32());

            $format->addGroup($group);
        }

        return $format;
    }

    private function readUInt16SharedFormat(StreamReader $fileReader, Format $format): void
    {
        $format->setLength($fileReader->readUInt16());
        $format->setLanguage($fileReader->readUInt16());
    }
}
