<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\IR\Utils\CMap\Format4;

class Transformer
{
    /**
     * @param int[] $startCodes
     * @param int[] $endCodes
     * @param int[] $idDeltas
     * @param int[] $idOffsets
     *
     * @return Segment[]
     */
    public static function arraysToSegments(array $startCodes, array $endCodes, array $idDeltas, array $idOffsets)
    {
        $segments = [];

        $size = \count($startCodes);
        for ($i = 0; $i < $size; ++$i) {
            $segment = new Segment();
            $segment->setStartCode($startCodes[$i]);
            $segment->setEndCode($endCodes[$i]);
            $segment->setIdDelta($idDeltas[$i]);
            $segment->setIdRangeOffset($idOffsets[$i]);

            $segments[] = $segment;
        }

        return $segments;
    }

    /**
     * @param int[] $glyphIndexAddresses
     *
     * @return int[]
     */
    public static function segmentToGlyphIndex(Segment $segment, int $segmentIndex, int $segmentCount, array $glyphIndexAddresses): array
    {
        if ($segment->getIdRangeOffset() === 0) {
            return self::rangeToGlyphIndex($segment->getStartCode(), $segment->getEndCode(), $segment->getIdDelta());
        }

        return self::offsetRangeToGlyphIndex($segment, $segmentIndex, $segmentCount, $glyphIndexAddresses);
    }

    /**
     * @return int[]
     */
    private static function rangeToGlyphIndex(int $startCode, int $endCode, int $idDelta): array
    {
        $glyphIndexes = [];

        for ($i = $startCode; $i <= $endCode; ++$i) {
            $glyphIndexes[$i] = ($i + $idDelta) % 65536;
        }

        return $glyphIndexes;
    }

    private static function offsetRangeToGlyphIndex(Segment $segment, int $segmentIndex, int $segmentCount, array $glyphIndexAddresses): array
    {
        $segmentOffset = $segmentCount - $segmentIndex; // until segment array finished
        $addressOffset = $segment->getIdRangeOffset() / 2 - $segmentOffset; // offset from beginning of glyph index addresses

        $glyphIndexes = [];
        $addressIndex = $addressOffset;
        for ($i = $segment->getStartCode(); $i <= $segment->getEndCode(); ++$i) {
            $glyphIndexes[$i] = $glyphIndexAddresses[$addressIndex++];
        }

        return $glyphIndexes;
    }
}
