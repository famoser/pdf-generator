<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Content;

use PdfGenerator\Backend\Content\Text\TextState;
use PdfGenerator\Backend\Content\Text\TextSymbol;
use PdfGenerator\IR\Configuration\PrintConfiguration;
use PdfGenerator\IR\Font\FontRepository;

class TextFactory
{
    /**
     * @var FontRepository
     */
    private $fontRepository;

    /**
     * TextBuilder constructor.
     *
     * @param FontRepository $fontRepository
     */
    public function __construct(FontRepository $fontRepository)
    {
        $this->fontRepository = $fontRepository;
    }

    /**
     * @param string $text
     * @param PrintConfiguration $printConfiguration
     *
     * @return TextSymbol[]
     */
    public function create(string $text, PrintConfiguration $printConfiguration)
    {
        $textState = $this->convertToTextState($printConfiguration);

        return [new TextSymbol($text, $textState)];
    }

    /**
     * @param PrintConfiguration $printConfiguration
     *
     * @return TextState
     */
    private function convertToTextState(PrintConfiguration $printConfiguration)
    {
        $textState = new TextState($this->fontRepository->get($printConfiguration->getFontFamily()), $printConfiguration->getFontSize());

        return $textState;
    }
}
