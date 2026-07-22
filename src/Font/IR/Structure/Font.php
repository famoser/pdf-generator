<?php

namespace Famoser\PdfGenerator\Font\IR\Structure;

use Famoser\PdfGenerator\Font\IR\Structure\Tables\FontInformation;

class Font
{
    private bool $isTrueTypeFont;

    /**
     * @var Character[]
     */
    private array $reservedCharacters;

    /**
     * @var Character[]
     */
    private array $characters = [];

    private FontInformation $fontInformation;

    private TableDirectory $tableDirectory;

    public function getIsTrueTypeFont(): bool
    {
        return $this->isTrueTypeFont;
    }

    public function setIsTrueTypeFont(bool $isTrueTypeFont): void
    {
        $this->isTrueTypeFont = $isTrueTypeFont;
    }

    /**
     * @return Character[]
     */
    public function getReservedCharacters(): array
    {
        return $this->reservedCharacters;
    }

    /**
     * @param Character[] $reservedCharacters
     */
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
