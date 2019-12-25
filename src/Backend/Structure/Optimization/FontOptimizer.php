<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Optimization;

use PdfGenerator\Backend\Structure\Optimization\FontOptimizer\FontOptimizerPayload;
use PdfGenerator\Font\IR\CharacterRepository;
use PdfGenerator\Font\IR\Structure\Font;

class FontOptimizer
{
    public function generateFontSubset(Font $font, string $usedText)
    {
        $orderedCodePoints = $this->getOrderedCodepoints($usedText);

        $characterRepository = new CharacterRepository($font);

        // build up newly needed characters
        $characters = [$font->getMissingGlyphCharacter()];
        $missingCodePoints = [];
        foreach ($orderedCodePoints as $index => $codePoint) {
            $character = $characterRepository->findByCodePoint($codePoint);
            if ($character !== null) {
                $characters[] = $character;
            } else {
                $missingCodePoints[$index] = $codePoint;
            }
        }

        $notEncodedCharIndexes = [];
        foreach ($missingCodePoints as $index => $value) {
            unset($orderedCodePoints[$index]);

            if ($value === 10) {
                $notEncodedCharIndexes[] = $index;
            }
        }

        foreach ($notEncodedCharIndexes as $notEncodedCharIndex) {
            unset($missingCodePoints[$notEncodedCharIndex]);
        }

        $orderedCodePoints = array_values($orderedCodePoints);
        $missingCodePoints = array_values($missingCodePoints);

        // todo: rename &create constructor
        return new FontOptimizerPayload();
    }

    /**
     * puts all used codepoints into ascending array.
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
}
