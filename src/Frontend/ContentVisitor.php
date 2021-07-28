<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend;

use PdfGenerator\Frontend\MeasuredContent\Image;
use PdfGenerator\Frontend\MeasuredContent\Paragraph;
use PdfGenerator\IR\Buffer\TextBuffer\MeasuredLine;
use PdfGenerator\IR\Buffer\TextBuffer\MeasuredPhrase;
use PdfGenerator\IR\Text\WordSizer\WordSizerInterface;

class ContentVisitor
{
    public function visitImage(Content\Image $param): Image
    {
        $image = \PdfGenerator\IR\Structure\Document\Image::create($param->getSrc());

        return new Image($image);
    }

    public function visitParagraph(Content\Paragraph $param): Paragraph
    {
        $paragraph = new Paragraph();

        foreach ($param->getPhrases() as $phrase) {
            $textStyle = $phrase->getTextStyle();

            $measuredLines = [];
            $lines = $this->splitAtNewlines($phrase->getText());
            $sizer = $this->wordSizerRepository->getWordSizer($textStyle->getFont());
            foreach ($lines as $line) {
                $measuredLines[] = $this->measureLine($line, $sizer);
            }

            $measuredPhrase = new MeasuredPhrase($measuredLines, $textStyle);
            $paragraph->addPhrase($measuredPhrase);
        }

        return $paragraph;
    }

    private function measureLine(string $line, WordSizerInterface $sizer)
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
    private function splitAtNewlines(string $text)
    {
        $textWithNormalizedNewlines = str_replace(["\r\n", "\n\r", "\r"], "\n", $text);

        return explode("\n", $textWithNormalizedNewlines);
    }
}
