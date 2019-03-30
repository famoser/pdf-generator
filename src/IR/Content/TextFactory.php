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

use PdfGenerator\Backend\Content\Operators\Level\TextLevel;
use PdfGenerator\Backend\Content\Operators\State\ColorState;
use PdfGenerator\Backend\Content\Operators\State\GeneralGraphicState;
use PdfGenerator\Backend\Content\Operators\State\TextState;
use PdfGenerator\Backend\Content\Symbols\TextSymbol;
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
     * @return TextLevel
     */
    private function convertToTextState(PrintConfiguration $printConfiguration)
    {
        $textState = new TextLevel(new GeneralGraphicState(), new ColorState(), new TextState());

        return $textState;
    }
}
