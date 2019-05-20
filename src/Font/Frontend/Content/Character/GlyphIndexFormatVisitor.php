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
use PdfGenerator\Font\Frontend\Structure\Table\CMap\Format\Format12;
use PdfGenerator\Font\Frontend\Structure\Table\CMap\Format\Format4;
use PdfGenerator\Font\Frontend\Structure\Table\CMap\Format\Format6;
use PdfGenerator\Font\Frontend\Structure\Table\CMap\VisitorInterface;

class GlyphIndexFormatVisitor implements VisitorInterface
{
    /**
     * @param Format $format
     *
     * @return string[]
     */
    public function visitFormat(Format $format)
    {
        return $format->accept($this);
    }

    /**
     * @param Format4 $format4
     *
     * @return string[]
     */
    public function visitFormat4(Format4 $format4)
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
     * @return mixed
     */
    public function visitFormat6(Format6 $format6)
    {
        // TODO: Implement visitFormat6() method.
    }

    /**
     * @param Format12 $format12
     *
     * @return mixed
     */
    public function visitFormat12(Format12 $format12)
    {
        // TODO: Implement visitFormat12() method.
    }
}
