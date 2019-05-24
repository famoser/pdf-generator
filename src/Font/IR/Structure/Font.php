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
     * @var Character
     */
    private $missingGlyphCharacter = null;

    /**
     * @var Character[]
     */
    private $characters = [];

    /**
     * @var FontFile
     */
    private $fontFile;

    /**
     * @var TableDirectory
     */
    private $tableDirectory;

    /**
     * @return Character
     */
    public function getMissingGlyphCharacter(): Character
    {
        return $this->missingGlyphCharacter;
    }

    /**
     * @param Character $missingGlyphCharacter
     */
    public function setMissingGlyphCharacter(Character $missingGlyphCharacter): void
    {
        $this->missingGlyphCharacter = $missingGlyphCharacter;
    }

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
     * @return TableDirectory
     */
    public function getTableDirectory(): TableDirectory
    {
        return $this->tableDirectory;
    }

    /**
     * @param TableDirectory $tableDirectory
     */
    public function setTableDirectory(TableDirectory $tableDirectory): void
    {
        $this->tableDirectory = $tableDirectory;
    }
}
