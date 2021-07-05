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

use PdfGenerator\IR\Structure\Document\Page\Content\Text\TextStyle;

class FontSizerRepository
{
    /**
     * @var WordSizerVisitor
     */
    private $wordSizerVisitor;

    /**
     * @var WordSizer[]
     */
    private $wordSizerByFont = [];

    public function __construct()
    {
        $this->wordSizerVisitor = new WordSizerVisitor();
    }

    public function getWordSizer(TextStyle $textStyle): WordSizer
    {
        $font = $textStyle->getFont();

        if (!\array_key_exists($font->getIdentifier(), $this->wordSizerByFont)) {
            /** @var WordSizer $wordSizer */
            $wordSizer = $font->accept($this->wordSizerVisitor);
            $this->wordSizerByFont[$font->getIdentifier()] = $wordSizer;
        }

        return $this->wordSizerByFont[$font->getIdentifier()];
    }
}
