<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\IR\Structure;

use PdfGenerator\Font\Frontend\File\FontFile;

class Font
{
    /**
     * @var Character[]
     */
    private $characters = [];

    /**
     * @var FontFile
     */
    private $fontFile;

    /**
     * @return Character[]
     */
    public function getCharacters(): array
    {
        return $this->characters;
    }

    /**
     * @param Character[] $characters
     */
    public function setCharacters(array $characters): void
    {
        $this->characters = $characters;
    }

    /**
     * @return FontFile
     */
    public function getFontFile(): FontFile
    {
        return $this->fontFile;
    }

    /**
     * @param FontFile $fontFile
     */
    public function setFontFile(FontFile $fontFile): void
    {
        $this->fontFile = $fontFile;
    }
}
