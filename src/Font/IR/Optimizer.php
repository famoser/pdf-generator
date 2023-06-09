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
use PdfGenerator\Font\IR\Structure\TableDirectory;

class Optimizer
{
    public static function create(): Optimizer
    {
        return new self();
    }

    /**
     * @param Character[] $characters
     *
     * @throws \Exception
     */
    public function getFontSubset(Font $source, array $characters): Font
    {
        $font = new Font();
        $font->setIsTrueTypeFont($source->getIsTrueTypeFont());

        $reservedCharacters = $source->getReservedCharacters();
        $characters = array_merge([], $characters);

        $this->ensureComponentCharactersIncluded($characters, $reservedCharacters);
        $this->sortCharactersByCodePoint($characters);

        $font->setReservedCharacters($reservedCharacters);
        $font->setCharacters($characters);

        $font->setTableDirectory($this->getTableDirectoryAfterSubsetting($source->getTableDirectory()));
        $font->setFontInformation($source->getFontInformation());

        return $font;
    }

    private function ensureComponentCharactersIncluded(array &$characters, array $reservedCharacters): void
    {
        // characters may be composed out of others, which need also be included in the subset
        /** @var Character[] $includedCharacters */
        $includedCharacters = [...$reservedCharacters, ...$characters];
        for ($i = 0; $i < \count($includedCharacters); ++$i) {
            $includedCharacter = $includedCharacters[$i];
            foreach ($includedCharacter->getComponentCharacters() as $componentCharacter) {
                if (!\in_array($componentCharacter, $includedCharacters, true)) {
                    $includedCharacters[] = $componentCharacter;
                    $characters[] = $componentCharacter;
                }
            }
        }
    }

    private function sortCharactersByCodePoint(array &$characters): void
    {
        $sortByCodePoint = function (Character $character1, Character $character2) {
            $unicodePoint1 = $character1->getUnicodePoint();
            $unicodePoint2 = $character2->getUnicodePoint();
            return $unicodePoint1 <=> $unicodePoint2;
        };

        usort($characters, $sortByCodePoint);
    }

    private function getTableDirectoryAfterSubsetting(TableDirectory $source): TableDirectory
    {
        $rawTableDirectory = new TableDirectory();

        $rawTableDirectory->setCvtTable($source->getCvtTable());
        $rawTableDirectory->setFpgmTable($source->getFpgmTable());

        /*
         * intentionally skipping GDEF, GPOST, GSUB as these are dependent on glyphs
         */

        $rawTableDirectory->setGaspTable($source->getGaspTable());
        $rawTableDirectory->setHeadTable($source->getHeadTable());
        $rawTableDirectory->setHHeaTable($source->getHHeaTable());
        $rawTableDirectory->setMaxPTable($source->getMaxPTable());
        $rawTableDirectory->setNameTable($source->getNameTable());
        $rawTableDirectory->setOS2Table($source->getOS2Table());
        $rawTableDirectory->setPostTable($source->getPostTable());
        $rawTableDirectory->setPrepTable($source->getPrepTable());

        // per default include unknown tables
        $rawTableDirectory->setRawTables($source->getRawTables());

        return $rawTableDirectory;
    }
}
