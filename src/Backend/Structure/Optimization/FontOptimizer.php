<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Optimization;

use PdfGenerator\Font\IR\CharacterRepository;
use PdfGenerator\Font\IR\Optimizer;
use PdfGenerator\Font\IR\Structure\Font;

class FontOptimizer
{
    /**
     * puts all used codepoints into ascending array.
     *
     * @param string $characters
     *
     * @return int[]
     */
    public function getOrderedCodepoints(string $characters): array
    {
        // split into characters (not bytes, like explode() or str_split() would)
        $characterArray = preg_split('//u', $characters, -1, PREG_SPLIT_NO_EMPTY);
        $uniqueCharacters = array_unique($characterArray);

        // get used codepoints
        $codePoints = [];
        foreach ($uniqueCharacters as $uniqueCharacter) {
            $codePoint = mb_ord($uniqueCharacter);
            $codePoints[] = $codePoint;
        }

        sort($codePoints);

        return $codePoints;
    }

    /**
     * @param Font $font
     * @param int[] $orderedCodePoints
     *
     * @throws \Exception
     *
     * @return Font
     */
    public function getFontSubset(Font $font, array $orderedCodePoints): Font
    {
        $characterRepository = new CharacterRepository($font);

        // build up newly needed characters
        $characters = [$font->getMissingGlyphCharacter()];
        foreach ($orderedCodePoints as $codePoint) {
            $character = $characterRepository->findByCodePoint($codePoint);
            $characters[] = $character;
        }

        // create subset
        $optimizer = Optimizer::create();
        $subset = $optimizer->getFontSubset($font, $characters);

        return $subset;
    }

    /**
     * @param int[] $orderedCodepoints
     *
     * @return int[]
     */
    public function getCharacterMappings(array $orderedCodepoints): array
    {
        /** @var int[] $characterMappings */
        $characterMappings = [];
        $totalCodePoints = \count($orderedCodepoints);
        $glyphIndex = 1;
        for ($i = 0; $i < $totalCodePoints; ++$i) {
            $characterMappings[$totalCodePoints] = $glyphIndex++;
        }

        return $characterMappings;
    }
}
