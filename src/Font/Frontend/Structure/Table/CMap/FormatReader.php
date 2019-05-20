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
use PdfGenerator\Font\Frontend\Structure\Table\CMap\Format\Format0;
use PdfGenerator\Font\Frontend\Structure\Table\CMap\Format\Format12;
use PdfGenerator\Font\Frontend\Structure\Table\CMap\Format\Format12Group;
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
            case 0:
                return $this->readFormat0($fileReader);
                break;
            case 4:
                return $this->readFormat4($fileReader, $startOffset);
                break;
            case 6:
                return $this->readFormat6($fileReader);
                break;
        }

        $fileReader->setOffset($startOffset);
        $formatFixed = $fileReader->readFixed();

        switch ($formatFixed) {
            case 12.0:
                return $this->readFormat12($fileReader);
                break;
        }

        return null;
    }

    /**
     * @param FileReader $fileReader
     *
     * @throws \Exception
     *
     * @return Format0
     */
    private function readFormat0(FileReader $fileReader)
    {
        $format = new Format0();

        $this->readUInt16SharedFormat($fileReader, $format);

        $format->setGlyphIndexArray($fileReader->readUInt8Array(256));

        return $format;
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

        $this->readUInt16SharedFormat($fileReader, $format);

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

        $this->readUInt16SharedFormat($fileReader, $format);

        $format->setFirstCode($fileReader->readUInt16());
        $format->setEntryCount($fileReader->readUInt16());
        $format->setGlyphIndexArray($fileReader->readUInt16Array($format->getEntryCount()));

        return $format;
    }

    /**
     * @param FileReader $fileReader
     *
     * @throws \Exception
     *
     * @return Format12
     */
    private function readFormat12(FileReader $fileReader)
    {
        $format = new Format12();

        $format->setLength($fileReader->readUInt32());
        $format->setLanguage($fileReader->readUInt32());
        $format->setNGroups($fileReader->readUInt32());

        for ($i = 0; $i < $format->getGroups(); ++$i) {
            $group = new Format12Group();

            $group->setStartCharCode($fileReader->readUInt32());
            $group->setEndCharCode($fileReader->readUInt32());
            $group->setStartGlyphCode($fileReader->readUInt32());

            $format->addGroup($group);
        }

        return $format;
    }

    /**
     * @param FileReader $fileReader
     * @param Format $format
     *
     * @throws \Exception
     */
    private function readUInt16SharedFormat(FileReader $fileReader, Format $format)
    {
        $format->setLength($fileReader->readUInt16());
        $format->setLanguage($fileReader->readUInt16());
    }
}
