<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\Structure\Optimization;

use Famoser\PdfGenerator\Font\Backend\FileWriter;
use Famoser\PdfGenerator\Font\IR\CharacterRepository;
use Famoser\PdfGenerator\Font\IR\Optimizer;
use Famoser\PdfGenerator\Font\IR\Structure\Font;

class FontOptimizer
{
    /**
     * @return array{Font,string,int[]}
     */
    public function createFontSubset(Font $font, string $charactersUsedInText): array
    {
        $usedCodepoints = $this->getOrderedCodepoints($charactersUsedInText);
        $characterRepository = new CharacterRepository($font);

        // extract needed characters
        $characters = [];
        foreach ($usedCodepoints as $codePoint) {
            $character = $characterRepository->findByCodePoint($codePoint);
            if (null !== $character) {
                $characters[] = $character;
            }
        }

        // create subset
        $optimizer = Optimizer::create();
        $font = $optimizer->getFontSubset($font, $characters);

        $writer = FileWriter::create();
        $fontData = $writer->writeFont($font);

        return [$font, $fontData, $usedCodepoints];
    }

    /**
     * puts all used codepoints into ascending array.
     *
     * @return int[]
     */
    public function getOrderedCodepoints(string $characters): array
    {
        // split into characters (not bytes, like explode() or str_split() would)
        $characterArray = preg_split('//u', $characters, -1, \PREG_SPLIT_NO_EMPTY);
        $uniqueCharacters = array_unique(/* @scrutinizer ignore-type */ $characterArray); // characterArray never false

        // get used codepoints
        $codePoints = [];
        foreach ($uniqueCharacters as $uniqueCharacter) {
            $codePoint = mb_ord($uniqueCharacter, 'UTF-8');
            $codePoints[] = $codePoint;
        }

        sort($codePoints);

        return $codePoints;
    }
}
