<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\IR;

use PdfGenerator\Font\IR\Structure\Character;
use PdfGenerator\Font\IR\Structure\Font;

class CharacterRepository
{
    /**
     * @var Font
     */
    private $font;

    /**
     * @var Character[]
     */
    private $charactersByCodePoint = [];

    /**
     * FontRepository constructor.
     */
    public function __construct(Font $font)
    {
        $this->font = $font;

        foreach ($font->getCharacters() as $character) {
            $this->charactersByCodePoint[$character->getUnicodePoint()] = $character;
        }
    }

    /**
     * @return Character
     */
    public function getMissingCharacter()
    {
        return $this->font->getMissingGlyphCharacter();
    }

    /**
     * @return Character
     */
    public function findByChar(string $character)
    {
        $codePoint = mb_ord($character);

        return $this->findByCodePoint($codePoint);
    }

    /**
     * @return Character
     */
    public function findByCodePoint(int $codePoint)
    {
        if (!\array_key_exists($codePoint, $this->charactersByCodePoint)) {
            return null;
        }

        return $this->charactersByCodePoint[$codePoint];
    }
}
