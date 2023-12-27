<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\LayoutEngine\Measure\Measurer;

use PdfGenerator\Frontend\Content\Paragraph\Phrase;
use PdfGenerator\Frontend\LayoutEngine\Measure\Measurement;
use PdfGenerator\Frontend\Resource\Font\FontMeasurement;
use PdfGenerator\Frontend\Resource\Font\FontRepository;

class ParagraphMeasurer
{
    private FontRepository $fontRepository;

    public function __construct()
    {
        $this->fontRepository = FontRepository::instance();
    }

    /**
     * @param Phrase[] $phrases
     */
    public function measure(array $phrases): Measurement
    {
        if (0 === count($phrases)) {
            return Measurement::zero();
        }

        [$width, $height] = $this->measureFirstWord($phrases[0]);
        $weight = 0.0;
        for ($i = 0; $i < count($phrases); ++$i) {
            $weight += $this->measureWeight($phrases[$i]);
        }

        return new Measurement($weight, $width, $height);
    }

    /**
     * @return float[]
     */
    private function measureFirstWord(Phrase $phrase): array
    {
        $textStyle = $phrase->getTextStyle();
        $fontMeasurement = $this->fontRepository->getFontMeasurement($textStyle);

        $firstWordLength = $this->measureFirstWordLength($phrase->getLines(), $fontMeasurement);

        return [$firstWordLength, $fontMeasurement->getLeading()];
    }

    /**
     * @param string[] $lines
     */
    private function measureFirstWordLength(array $lines, FontMeasurement $fontMeasurement): float
    {
        if (0 === count($lines)) {
            return 0;
        }

        $firstLine = $lines[0];
        if ('' === $firstLine) {
            return 0;
        }

        $endOfFirstWord = strpos($firstLine, ' ');
        if (0 === $endOfFirstWord) {
            return $fontMeasurement->getSpaceWidth();
        }

        $firstWord = $endOfFirstWord > 0 ? substr($firstLine, 0, $endOfFirstWord) : $firstLine;

        return $fontMeasurement->getWidth($firstWord);
    }

    private function measureWeight(Phrase $phrase): float
    {
        $textStyle = $phrase->getTextStyle();
        $fontMeasurement = $this->fontRepository->getFontMeasurement($textStyle);

        $weight = 0.0;
        foreach ($phrase->getLines() as $line) {
            $spaceWidth = $fontMeasurement->getSpaceWidth();

            $lineLength = 0;
            $words = explode(' ', $line);
            foreach ($words as $word) {
                $wordSize = $fontMeasurement->getWidth($word);
                $lineLength += $wordSize + $spaceWidth;
            }

            if ($lineLength > 0) {
                $lineLength -= $spaceWidth;
            }

            $weight += $lineLength * $fontMeasurement->getLeading();
        }

        return $weight;
    }
}
