<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\Structure\Table\CMap;

use PdfGenerator\Font\Frontend\FileReader;
use PdfGenerator\Font\Frontend\Structure\Table\CMap\Format\Format;
use PdfGenerator\Font\Frontend\Structure\Table\CMap\Format\Format4;
use PdfGenerator\Font\Frontend\Structure\Table\CMap\Format\Format6;

class FormatReader
{
    /**
     * @param FileReader $fileReader
     *
     * @throws \Exception
     *
     * @return Format|null
     */
    public function readFormat(FileReader $fileReader)
    {
        $startOffset = $fileReader->getOffset();
        $format = $fileReader->readUInt16();
        switch ($format) {
            case 4:
                return $this->readFormat4($fileReader, $startOffset);
                break;
            case 6:
                return $this->readFormat6($fileReader);
                break;
        }

        return null;
    }

    /**
     * @param FileReader $fileReader
     * @param int $startOffset
     *
     * @throws \Exception
     *
     * @return Format4
     */
    private function readFormat4(FileReader $fileReader, int $startOffset)
    {
        $format = new Format4();

        $this->readSharedFormat($fileReader, $format);

        $format->setSegCountX2($fileReader->readUInt16());
        $format->setSearchRange($fileReader->readUInt16());
        $format->setEntrySelector($fileReader->readUInt16());
        $format->setRangeShift($fileReader->readUInt16());

        $segCount = $format->getSegCountX2() / 2;
        $format->setEndCodes($fileReader->readUInt16Array($segCount));
        $format->setReservedPad($fileReader->readUInt16());
        $format->setStartCodes($fileReader->readUInt16Array($segCount));
        $format->setIdDeltas($fileReader->readUInt16Array($segCount));
        $format->setIdRangeOffsets($fileReader->readUInt16Array($segCount));

        $tableEnd = $startOffset + $format->getLength();
        $glyphIndexes = ($tableEnd - $fileReader->getOffset()) / 2;
        $format->setGlyphIndexArray($fileReader->readUInt16Array($glyphIndexes));

        return $format;
    }

    /**
     * @param FileReader $fileReader
     *
     * @throws \Exception
     *
     * @return Format6
     */
    private function readFormat6(FileReader $fileReader)
    {
        $format = new Format6();

        $this->readSharedFormat($fileReader, $format);

        $format->setFirstCode($fileReader->readUInt16());
        $format->setEntryCount($fileReader->readUInt16());
        $format->setGlyphIndexArray($fileReader->readUInt16Array($format->getEntryCount()));

        return $format;
    }

    /**
     * @param FileReader $fileReader
     * @param Format $format
     *
     * @throws \Exception
     */
    private function readSharedFormat(FileReader $fileReader, Format $format)
    {
        $format->setLength($fileReader->readUInt16());
        $format->setLanguage($fileReader->readUInt16());
    }
}
