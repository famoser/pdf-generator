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

use PdfGenerator\Font\IR\Structure\Tables\FontInformation;

class Font
{
    /**
     * @var Character[]
     */
    private $reservedCharacters;

    /**
     * @var Character[]
     */
    private $characters = [];

    /**
     * @var FontInformation
     */
    private $fontInformation;

    /**
     * @var TableDirectory
     */
    private $tableDirectory;

    public function getReservedCharacters(): array
    {
        return $this->reservedCharacters;
    }

    public function setReservedCharacters(array $reservedCharacters): void
    {
        $this->reservedCharacters = $reservedCharacters;
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

    public function getTableDirectory(): TableDirectory
    {
        return $this->tableDirectory;
    }

    public function setTableDirectory(TableDirectory $tableDirectory): void
    {
        $this->tableDirectory = $tableDirectory;
    }

    public function getFontInformation(): FontInformation
    {
        return $this->fontInformation;
    }

    public function setFontInformation(FontInformation $fontInformation): void
    {
        $this->fontInformation = $fontInformation;
    }
}
