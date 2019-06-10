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

use PdfGenerator\Backend\Structure\Document\Font\CharacterMapping;
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
     * @return CharacterMapping[]
     */
    public function getCharacterMappings($orderedCodepoints): array
    {
        /** @var CharacterMapping[] $characterMaps */
        $characterMappings = [];
        $totalCodePoints = \count($orderedCodepoints);
        for ($i = 0; $i < $totalCodePoints; ++$i) {
            $startCodePoint = $orderedCodepoints[$i];
            $startCharacterIndex = $i + 1; // +1 because at 0 is the missing glyph character

            $sizeOfRange = 1;
            for (; $i < $totalCodePoints - 1; $i++, $sizeOfRange++) {
                $nextCodePoint = $orderedCodepoints[$i + 1];

                // stop if no longer follow immediately
                if ($nextCodePoint - $startCodePoint !== $sizeOfRange) {
                    break;
                }
            }

            // add new range to character mappings
            $endCodePoint = $orderedCodepoints[$i];
            $characterMapping = new CharacterMapping($startCodePoint, $endCodePoint, $startCharacterIndex);
            $characterMappings[] = $characterMapping;
        }

        return $characterMappings;
    }
}
