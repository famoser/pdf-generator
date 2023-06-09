<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\IR\Utils\CMap;

use PdfGenerator\Font\Frontend\File\Table\CMap\Format\Format;
use PdfGenerator\Font\Frontend\File\Table\CMap\Format\Format0;
use PdfGenerator\Font\Frontend\File\Table\CMap\Format\Format12;
use PdfGenerator\Font\Frontend\File\Table\CMap\Format\Format4;
use PdfGenerator\Font\Frontend\File\Table\CMap\Format\Format6;
use PdfGenerator\Font\Frontend\File\Table\CMap\VisitorInterface;
use PdfGenerator\Font\IR\Utils\CMap\Format4\Transformer;

class GlyphIndexFormatVisitor implements VisitorInterface
{
    /**
     * @return int[]
     */
    public function visitFormat(Format $format): array
    {
        return $format->accept($this);
    }

    /**
     * @return int[]
     */
    public function visitFormat0(Format0 $format0): array
    {
        return array_values($format0->getGlyphIndexArray());
    }

    /**
     * @return int[]
     */
    public function visitFormat4(Format4 $format4): array
    {
        $segments = Transformer::arraysToSegments($format4->getStartCodes(), $format4->getEndCodes(), $format4->getIdDeltas(), $format4->getIdRangeOffsets());

        $glyphIndexArrays = [];
        $segmentCount = \count($segments);
        for ($i = 0; $i < $segmentCount; ++$i) {
            $segmentGlyphIndexes = Transformer::segmentToGlyphIndex($segments[$i], $i, $segmentCount, $format4->getGlyphIndexArray());
            $glyphIndexArrays[] = $segmentGlyphIndexes;
        }

        $glyphIndexes = [];
        foreach ($glyphIndexArrays as $glyphIndexArray) {
            foreach ($glyphIndexArray as $key => $value) {
                $glyphIndexes[$key] = $value;
            }
        }

        return $glyphIndexes;
    }

    /**
     * @return int[]
     */
    public function visitFormat6(Format6 $format6): array
    {
        $glyphIndexes = [];

        for ($i = $format6->getFirstCode(), $zeroBased = 0; $i < $format6->getEntryCount(); $i++, $zeroBased++) {
            $glyphIndexes[$i] = $format6->getGlyphIndexArray()[$zeroBased];
        }

        return $glyphIndexes;
    }

    /**
     * @return int[]
     */
    public function visitFormat12(Format12 $format12): array
    {
        $glyphIndexes = [];
        foreach ($format12->getGroups() as $group) {
            $code = $group->getStartCharCode();
            $zeroBased = 0;
            while ($code !== $group->getEndCharCode()) {
                $glyphIndexes[$code] = $group->getStartGlyphCode() + $zeroBased;

                ++$zeroBased;
                ++$code;
            }
        }

        return $glyphIndexes;
    }
}
