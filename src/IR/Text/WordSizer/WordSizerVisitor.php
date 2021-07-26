<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Text\WordSizer;

use PdfGenerator\IR\Structure\Document\Font\DefaultFont;
use PdfGenerator\IR\Structure\Document\Font\EmbeddedFont;
use PdfGenerator\IR\Structure\Document\Font\FontVisitor;

class WordSizerVisitor implements FontVisitor
{
    public function visitDefaultFont(DefaultFont $param)
    {
        $filename = $param->getFont() . '_' . $param->getStyle() . '.json';
        $path = __DIR__ . \DIRECTORY_SEPARATOR . 'DefaultFont' . \DIRECTORY_SEPARATOR . $filename;
        $characterSizesJson = file_get_contents($path);
        $characterSizes = json_decode($characterSizesJson, true);

        if ($characterSizes['isMonospace']) {
            return new MonospaceWordSizer($characterSizes['invalidCharacterWidth']);
        }

        return new WordSizer($characterSizes['invalidCharacterWidth'], $characterSizes['characterAdvanceWidthLookup']);
    }

    public function visitEmbeddedFont(EmbeddedFont $param)
    {
        $characterSizer = new CharacterSizer($param->getFont());
        if ($characterSizer->isMonospace()) {
            return new MonospaceWordSizer($characterSizer->getInvalidCharacterWidth());
        }

        return new WordSizer($characterSizer->getInvalidCharacterWidth(), $characterSizer->getCharacterAdvanceWidthLookup());
    }
}
