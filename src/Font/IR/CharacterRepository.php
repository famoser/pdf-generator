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
     *
     * @param Font $font
     */
    public function __construct(Font $font)
    {
        $this->font = $font;

        foreach ($font->getCharacters() as $character) {
            $this->charactersByCodePoint[$character->getUnicodePoint()] = $character;
        }
    }

    /**
     * @param string $character
     *
     * @return Character
     */
    public function find(string $character)
    {
        $codePoint = mb_ord($character);

        return $this->charactersByCodePoint[$codePoint];
    }
}
