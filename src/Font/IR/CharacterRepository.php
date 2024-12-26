<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Font\IR;

use Famoser\PdfGenerator\Font\IR\Structure\Character;
use Famoser\PdfGenerator\Font\IR\Structure\Font;

class CharacterRepository
{
    /**
     * @var Character[]
     */
    private array $charactersByCodePoint = [];

    public function __construct(Font $font)
    {
        foreach ($font->getCharacters() as $character) {
            $this->charactersByCodePoint[$character->getUnicodePoint()] = $character;
        }
    }

    public function findByChar(string $character): ?Character
    {
        $codePoint = mb_ord($character, 'UTF-8');

        return $this->findByCodePoint($codePoint);
    }

    public function findByCodePoint(int $codePoint): ?Character
    {
        if (!\array_key_exists($codePoint, $this->charactersByCodePoint)) {
            return null;
        }

        return $this->charactersByCodePoint[$codePoint];
    }
}
