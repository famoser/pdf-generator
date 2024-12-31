<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\LayoutEngine\Measure\Measurer;

use Famoser\PdfGenerator\Frontend\Layout\TextSpan;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Measure\Measurement;
use Famoser\PdfGenerator\Frontend\Resource\Font\FontMeasurement;
use Famoser\PdfGenerator\Frontend\Resource\Font\FontRepository;

readonly class TextMeasurer
{
    private FontRepository $fontRepository;

    public function __construct()
    {
        $this->fontRepository = FontRepository::instance();
    }

    /**
     * @param TextSpan[] $spans
     */
    public function measure(array $spans): Measurement
    {
        if (0 === count($spans)) {
            return Measurement::zero();
        }

        [$width, $height] = $this->measureFirstWord($spans[0]);
        $weight = 0.0;
        for ($i = 0; $i < count($spans); ++$i) {
            $weight += $this->measureWeight($spans[$i]);
        }

        return new Measurement($weight, $width, $height);
    }

    /**
     * @return float[]
     */
    private function measureFirstWord(TextSpan $span): array
    {
        $textStyle = $span->getTextStyle();
        $fontMeasurement = $this->fontRepository->getFontMeasurement($textStyle);

        $firstWordLength = $this->measureFirstWordLength($span->getLines(), $fontMeasurement);

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

    private function measureWeight(TextSpan $span): float
    {
        $textStyle = $span->getTextStyle();
        $fontMeasurement = $this->fontRepository->getFontMeasurement($textStyle);

        $weight = 0.0;
        foreach ($span->getLines() as $line) {
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
