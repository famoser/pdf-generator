<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Content\Font;

use PdfGenerator\Backend\Structure\Font\Type0;
use PdfGenerator\Font\IR\CharacterRepository;
use PdfGenerator\Font\IR\Structure\Character;
use PdfGenerator\Font\IR\Structure\Font;

class Type0Container
{
    /**
     * @var Type0
     */
    private $type0Font;

    /**
     * @var Font
     */
    private $font;

    /**
     * @var string[]
     */
    private $mapping = [];

    /**
     * @var CharacterRepository
     */
    private $characterRepository;

    /**
     * @var Character[]
     */
    private $mappedCharacters = [];

    /**
     * @return Type0
     */
    public function getType0Font(): Type0
    {
        return $this->type0Font;
    }

    /**
     * @param Type0 $type0Font
     */
    public function setType0Font(Type0 $type0Font): void
    {
        $this->type0Font = $type0Font;
    }

    /**
     * @return Font
     */
    public function getFont(): Font
    {
        return $this->font;
    }

    /**
     * @param Font $font
     */
    public function setFont(Font $font): void
    {
        $this->characterRepository = new CharacterRepository($font);
        $this->font = $font;
    }

    /**
     * @param string $char
     *
     * @return string
     */
    public function getOrCreateMapping(string $char)
    {
        $codepoint = mb_ord($char);
        if (\array_key_exists($codepoint, $this->mapping)) {
            return $this->mapping[$codepoint];
        }

        $character = $this->characterRepository->findByCodePoint($codepoint);
        if ($character === null) {
            $character = $this->characterRepository->getMissingCharacter();
        }

        $this->mappedCharacters[] = $character;

        $charMapping = $this->getTwoByteString(\count($this->mappedCharacters));
        $this->mapping[$codepoint] = $charMapping;

        return $charMapping;
    }

    private function getTwoByteString(int $number)
    {
    }

    /**
     * @return Character[]
     */
    public function getMappedCharacters(): array
    {
        return $this->mappedCharacters;
    }
}
