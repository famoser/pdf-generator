<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\CursorPrinter\Buffer;

use PdfGenerator\Frontend\CursorPrinter\Buffer\TextBuffer\MeasuredLine;
use PdfGenerator\Frontend\CursorPrinter\Buffer\TextBuffer\MeasuredParagraph;
use PdfGenerator\Frontend\CursorPrinter\Buffer\TextBuffer\MeasuredPhrase;
use PdfGenerator\Frontend\CursorPrinter\Buffer\TextBuffer\Phrase;
use PdfGenerator\Frontend\WordSizer\WordSizerInterface;
use PdfGenerator\Frontend\WordSizer\WordSizerRepository;
use PdfGenerator\IR\Document\Content\Text\TextStyle;

class TextBuffer
{
    /**
     * @var Phrase[]
     */
    private array $phrases = [];

    private readonly WordSizerRepository $wordSizerRepository;

    public function __construct()
    {
        $this->wordSizerRepository = new WordSizerRepository();
    }

    public function add(TextStyle $textStyle, string $text): void
    {
        $phrase = new Phrase();
        $phrase->setText($text);
        $phrase->setTextStyle($textStyle);

        $this->phrases[] = $phrase;
    }

    public function getMeasuredParagraph(): MeasuredParagraph
    {
        $measuredParagraph = new MeasuredParagraph();

        foreach ($this->phrases as $phrase) {
            $textStyle = $phrase->getTextStyle();

            $measuredLines = [];
            $lines = $this->splitAtNewlines($phrase->getText());
            $sizer = $this->wordSizerRepository->getWordSizer($textStyle->getFont());
            foreach ($lines as $line) {
                $measuredLines[] = $this->measureLine($line, $sizer);
            }

            $measuredPhrase = new MeasuredPhrase($measuredLines, $textStyle);
            $measuredParagraph->addMeasuredPhrase($measuredPhrase);
        }

        return $measuredParagraph;
    }

    private function measureLine(string $line, WordSizerInterface $sizer): MeasuredLine
    {
        $words = explode(' ', $line);

        $wordWidths = [];
        foreach ($words as $word) {
            $wordWidths[] = $sizer->getWidth($word);
        }

        return new MeasuredLine($words, $wordWidths, $sizer->getSpaceWidth());
    }

    /**
     * @return string[]
     */
    private function splitAtNewlines(string $text): array
    {
        $textWithNormalizedNewlines = str_replace(["\r\n", "\n\r", "\r"], "\n", $text);

        return explode("\n", $textWithNormalizedNewlines);
    }
}