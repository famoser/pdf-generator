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

use PdfGenerator\Font\IR\Structure\Font;
use PdfGenerator\Font\IR\Structure\RawTableDirectory;

class Optimizer
{
    /**
     * @param Font $source
     * @param array $characters
     *
     * @return Font
     */
    public function getFontSubset(Font $source, array $characters)
    {
        $font = new Font();

        $font->setMissingGlyphCharacter($source->getMissingGlyphCharacter());
        $font->setCharacters($characters);
        $font->setRawTableDirectory($this->getRawTablesAfterSubsetting($source->getRawTableDirectory()));

        return $font;
    }

    /**
     * @param RawTableDirectory $source
     *
     * @return RawTableDirectory
     */
    private function getRawTablesAfterSubsetting(RawTableDirectory $source)
    {
        $rawTableDirectory = new RawTableDirectory();

        $rawTableDirectory->setCvtTable($source->getCvtTable());
        $rawTableDirectory->setFpgmTable($source->getFpgmTable());
        $rawTableDirectory->setGaspTable($source->getGaspTable());
        $rawTableDirectory->setOS2Table($source->getOS2Table());
        $rawTableDirectory->setPrepTable($source->getPrepTable());

        // per default include unknown tables
        $rawTableDirectory->setRawTables($source->getRawTables());

        return $rawTableDirectory;
    }
}
