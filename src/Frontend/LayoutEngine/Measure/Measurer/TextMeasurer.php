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
use Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate\Allocators\TextAllocator;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Measure\Measurement;
use Famoser\PdfGenerator\Frontend\Resource\Font\FontMeasurement;
use Famoser\PdfGenerator\Frontend\Resource\Font\FontRepository;
use mysql_xdevapi\SqlStatementResult;

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
        $fontMeasurement = $this->fontRepository->getFontMeasurement($span);

        $firstChunk = TextAllocator::getChunk($span->getText());
        $firstChunkLength = $fontMeasurement->getWidth($firstChunk);

        return [$firstChunkLength, $fontMeasurement->getLeading()];
    }

    private function measureWeight(TextSpan $span): float
    {
        $fontMeasurement = $this->fontRepository->getFontMeasurement($span);

        $weight = 0.0;
        $currentText = $span->getText();
        while ($currentText !== null) {
            $nextLines = '';
            $line = TextAllocator::getLine($currentText, $nextLines);

            $lineLength = 0.0;
            while ($line != null) {
                $nextChunks = '';
                $chunk = TextAllocator::getChunk($line, $nextChunks);
                $lineLength += $fontMeasurement->getWidth($chunk);
                $line = $nextChunks;
            }

            $weight += $lineLength * $fontMeasurement->getLeading();
            $currentText = $nextLines;
        }

        return $weight;
    }
}
