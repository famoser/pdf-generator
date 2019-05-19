<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\Structure;

use PdfGenerator\Font\Frontend\FileReader;
use PdfGenerator\Font\Frontend\Structure\Table\CMapFormat\Format;
use PdfGenerator\Font\Frontend\Structure\Table\CMapFormat\Format4;

class CMapFormatReader
{
    /**
     * @param FileReader $fileReader
     * @param int $startOffset
     *
     * @throws \Exception
     *
     * @return Format4
     */
    public function readFormat4(FileReader $fileReader, int $startOffset)
    {
        $format4 = new Format4();

        $this->readFormat($fileReader, $format4);

        $format4->setSegCountX2($fileReader->readUInt16());
        $format4->setSearchRange($fileReader->readUInt16());
        $format4->setEntrySelector($fileReader->readUInt16());
        $format4->setRangeShift($fileReader->readUInt16());

        $segCount = $format4->getSegCountX2() / 2;
        $format4->setEndCodes($fileReader->readUInt16Array($segCount));
        $format4->setReservedPad($fileReader->readUInt16());
        $format4->setStartCodes($fileReader->readUInt16Array($segCount));
        $format4->setIdDeltas($fileReader->readUInt16Array($segCount));
        $format4->setIdRangeOffsets($fileReader->readUInt16Array($segCount));

        $tableEnd = $startOffset + $format4->getLength();
        $glyphIndexes = $tableEnd - $fileReader->getOffset() / 2;
        $format4->setGlyphIndexArray($fileReader->readUInt16Array($glyphIndexes));

        return $format4;
    }

    /**
     * @param FileReader $fileReader
     * @param Format $format
     *
     * @throws \Exception
     */
    private function readFormat(FileReader $fileReader, Format $format)
    {
        $format->setLength($fileReader->readUInt16());
        $format->setLanguage($fileReader->readUInt16());
    }
}
