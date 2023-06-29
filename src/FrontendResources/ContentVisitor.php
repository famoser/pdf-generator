<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\FrontendResources;

use PdfGenerator\Frontend\Font\FontRepository;
use PdfGenerator\Frontend\Font\MeasuredContent\Image;
use PdfGenerator\Frontend\Font\MeasuredContent\Paragraph;
use PdfGenerator\Frontend\Font\MeasuredContent\Rectangle;
use PdfGenerator\Frontend\Font\WordSizer\WordSizerInterface;
use PdfGenerator\Frontend\Font\WordSizer\WordSizerRepository;
use PdfGenerator\FrontendResources\MeasuredContent\Utils\ImageRepository;

class ContentVisitor
{
    public function __construct(private readonly ImageRepository $imageRespository, private readonly FontRepository $fontRepository, private readonly WordSizerRepository $wordSizerRepository)
    {
    }

    public function visitImage(\PdfGenerator\Frontend\Content\ImagePlacement $param): Image
    {
        $image = $this->imageRespository->getImage($param);

        return new Image($image, $param->getStyle());
    }

    public function visitParagraph(\PdfGenerator\Frontend\Content\Paragraph $param): Paragraph
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

            $measuredPhrase = new MeasuredContent\Paragraph\Phrase($measuredLines, $textStyle, $font);
            $paragraph->addPhrase($measuredPhrase);
        }

        return $paragraph;
    }

    public function visitRectangle(\PdfGenerator\Frontend\Content\Rectangle $param): Rectangle
    {
        return new Rectangle($param->getStyle(), $param);
    }

    private function measureLine(string $line, WordSizerInterface $sizer): MeasuredContent\Paragraph\Line
    {
        $words = explode(' ', $line);

        $wordWidths = [];
        foreach ($words as $word) {
            $wordWidths[] = $sizer->getWidth($word);
        }

        return new MeasuredContent\Paragraph\Line($words, $wordWidths, $sizer->getSpaceWidth());
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
