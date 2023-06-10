<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\WordSizer;

use PdfGenerator\Font\IR\CharacterSizer;
use PdfGenerator\IR\Document\Font\DefaultFont;
use PdfGenerator\IR\Document\Font\EmbeddedFont;
use PdfGenerator\IR\Document\Font\FontVisitor;

class WordSizerVisitor implements FontVisitor
{
    public function visitDefaultFont(DefaultFont $param): MonospaceProportionalWordSizer|ProportionalWordSizer
    {
        $filename = $param->getFont().'_'.$param->getStyle().'.json';
        $path = __DIR__.\DIRECTORY_SEPARATOR.'DefaultFont'.\DIRECTORY_SEPARATOR.$filename;
        $characterSizesJson = file_get_contents($path);
        $characterSizes = json_decode($characterSizesJson, true, 512, JSON_THROW_ON_ERROR);

        if ($characterSizes['isMonospace']) {
            return new MonospaceProportionalWordSizer($characterSizes['invalidCharacterWidth']);
        }

        return new ProportionalWordSizer($characterSizes['invalidCharacterWidth'], $characterSizes['characterAdvanceWidthLookup']);
    }

    public function visitEmbeddedFont(EmbeddedFont $param): MonospaceProportionalWordSizer|ProportionalWordSizer
    {
        $characterSizer = new CharacterSizer($param->getFont());
        if ($characterSizer->isMonospace()) {
            return new MonospaceProportionalWordSizer($characterSizer->getInvalidCharacterWidth());
        }

        return new ProportionalWordSizer($characterSizer->getInvalidCharacterWidth(), $characterSizer->getCharacterAdvanceWidthLookup());
    }
}
