<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\Content\Character;

use PdfGenerator\Font\Frontend\Content\Character\Format4\Transformer;
use PdfGenerator\Font\Frontend\Structure\Table\CMap\Format\Format;
use PdfGenerator\Font\Frontend\Structure\Table\CMap\Format\Format0;
use PdfGenerator\Font\Frontend\Structure\Table\CMap\Format\Format12;
use PdfGenerator\Font\Frontend\Structure\Table\CMap\Format\Format4;
use PdfGenerator\Font\Frontend\Structure\Table\CMap\Format\Format6;
use PdfGenerator\Font\Frontend\Structure\Table\CMap\VisitorInterface;

class GlyphIndexFormatVisitor implements VisitorInterface
{
    /**
     * @param Format $format
     *
     * @return int[]
     */
    public function visitFormat(Format $format)
    {
        return $format->accept($this);
    }

    /**
     * @param Format0 $format0
     *
     * @return int[]
     */
    public function visitFormat0(Format0 $format0): array
    {
        return array_values($format0->getGlyphIndexArray());
    }

    /**
     * @param Format4 $format4
     *
     * @return int[]
     */
    public function visitFormat4(Format4 $format4): array
    {
        $segments = Transformer::arraysToSegments($format4->getStartCodes(), $format4->getEndCodes(), $format4->getIdDeltas(), $format4->getIdRangeOffsets());

        $glyphIndexes = [];

        $segmentCount = \count($segments);
        for ($i = 0; $i < $segmentCount; ++$i) {
            $segmentGlyphIndexes = Transformer::segmentToGlyphIndex($segments[$i], $i, $segmentCount, $format4->getGlyphIndexArray());
            $glyphIndexes = array_merge($glyphIndexes, $segmentGlyphIndexes);
        }

        return $glyphIndexes;
    }

    /**
     * @param Format6 $format6
     *
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
     * @param Format12 $format12
     *
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
