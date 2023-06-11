<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\FrontendResources\WordSizer;

use PdfGenerator\IR\Document\Resource\Font;

class WordSizerRepository
{
    private readonly WordSizerVisitor $wordSizerVisitor;

    /**
     * @var WordSizerInterface[]
     */
    private array $wordSizerByFont = [];

    public function __construct()
    {
        $this->wordSizerVisitor = new WordSizerVisitor();
    }

    public function getWordSizer(Font $font): WordSizerInterface
    {
        if (!\array_key_exists($font->getIdentifier(), $this->wordSizerByFont)) {
            /** @var WordSizerInterface $wordSizer */
            $wordSizer = $font->accept($this->wordSizerVisitor);
            $this->wordSizerByFont[$font->getIdentifier()] = $wordSizer;
        }

        return $this->wordSizerByFont[$font->getIdentifier()];
    }
}
