<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Text\LineBreak\WordSizer;

use PdfGenerator\IR\Structure\Document\Font\DefaultFont;
use PdfGenerator\IR\Structure\Document\Font\EmbeddedFont;
use PdfGenerator\IR\Structure\Document\Font\FontVisitor;

class WordSizerVisitor implements FontVisitor
{
    public function visitDefaultFont(DefaultFont $param)
    {
        throw new \Exception('Not implemented');
    }

    public function visitEmbeddedFont(EmbeddedFont $param)
    {
        $characterSizer = new CharacterSizer($param->getFont());

        return new WordSizer($characterSizer->getInvalidCharacterWidth(), $characterSizer->getCharacterAdvanceWidthLookup());
    }
}
