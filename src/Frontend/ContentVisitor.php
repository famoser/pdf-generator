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
use PdfGenerator\Frontend\MeasuredContent\Rectangle;
use PdfGenerator\Frontend\MeasuredContent\Utils\FontRepository;
use PdfGenerator\Frontend\MeasuredContent\Utils\ImageRepository;
use PdfGenerator\IR\Text\WordSizer\WordSizerInterface;
use PdfGenerator\IR\Text\WordSizer\WordSizerRepository;

class ContentVisitor
{
    /**
     * ContentVisitor constructor.
     */
    public function __construct(private ImageRepository $imageRespository, private FontRepository $fontRepository, private WordSizerRepository $wordSizerRepository)
    {
    }

    public function visitImage(Content\Image $param): Image
    {
        $image = $this->imageRespository->getImage($param);

        return new Image($image, $param->getStyle());
    }

    public function visitParagraph(Content\Paragraph $param): Paragraph
    {
        $paragraph = new Paragraph($param->getStyle());

        foreach ($param->getPhrases() as $phrase) {
            $textStyle = $phrase->getTextStyle();

            $measuredLines = [];
            $lines = $this->splitAtNewlines($phrase->getText());
            $font = $this->fontRepository->getFont($phrase->getTextStyle()->getFont());
            $sizer = $this->wordSizerRepository->getWordSizer($font);
            foreach ($lines as $line) {
                $measuredLines[] = $this->measureLine($line, $sizer);
            }

            $measuredPhrase = new Paragraph\Phrase($measuredLines, $textStyle, $font);
            $paragraph->addPhrase($measuredPhrase);
        }

        return $paragraph;
    }

    public function visitRectangle(Content\Rectangle $param): Rectangle
    {
        return new Rectangle($param->getStyle(), $param);
    }

    private function measureLine(string $line, WordSizerInterface $sizer): Paragraph\Line
    {
        $words = explode(' ', $line);

        $wordWidths = [];
        foreach ($words as $word) {
            $wordWidths[] = $sizer->getWidth($word);
        }

        return new Paragraph\Line($words, $wordWidths, $sizer->getSpaceWidth());
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
