<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Text\LineBreak\FontSizer;

use PdfGenerator\IR\Structure\Document\Page\Content\Text\TextStyle;

class FontSizerRepository
{
    /**
     * @var FontSizerVisitor
     */
    private $wordSizerVisitor;

    /**
     * @var ResizableFontSizer[]
     */
    private $wordSizerByFont = [];

    public function __construct()
    {
        $this->wordSizerVisitor = new FontSizerVisitor();
    }

    public function getFontSizer(TextStyle $textStyle): FontSizer
    {
        $font = $textStyle->getFont();

        if (!\array_key_exists($font->getIdentifier(), $this->wordSizerByFont)) {
            /** @var ResizableFontSizer $wordSizer */
            $wordSizer = $font->accept($this->wordSizerVisitor);
            $this->wordSizerByFont[$font->getIdentifier()] = $wordSizer;
        }

        $wordSizer = $this->wordSizerByFont[$font->getIdentifier()];
        $wordSizer->setFontSize($textStyle->getFontSize());

        return $wordSizer;
    }
}
