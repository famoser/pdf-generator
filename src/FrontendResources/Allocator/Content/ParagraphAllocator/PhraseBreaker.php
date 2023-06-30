<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\FrontendResources\Allocator\Content\ParagraphAllocator;

use PdfGenerator\Frontend\Resource\Font\FontMeasurement;
use PdfGenerator\FrontendResources\LocatedContent\Paragraph\Fragment;
use PdfGenerator\FrontendResources\MeasuredContent\Paragraph\Phrase;

class PhraseBreaker
{
    /**
     * the next to be included word.
     */
    private int $nextLineIndex = 0;

    private ?LineBreaker $lineBreaker = null;

    public function __construct(private readonly Phrase $phrase, private readonly FontMeasurement $fontMeasurement)
    {
    }

    public function getPhrase(): Phrase
    {
        return $this->phrase;
    }

    public function getFontMeasurement(): FontMeasurement
    {
        return $this->fontMeasurement;
    }

    public function isEmpty(): bool
    {
        return (null === $this->lineBreaker || $this->lineBreaker->isEmpty()) &&
            $this->nextLineIndex >= \count($this->phrase->getMeasuredLines());
    }

    public function nextFragment(float $targetWidth, bool $allowEmpty): ?Fragment
    {
        \assert(!$this->isEmpty());

        if (null === $this->lineBreaker || $this->lineBreaker->isEmpty()) {
            $nextLine = $this->phrase->getMeasuredLines()[$this->nextLineIndex++];
            $this->lineBreaker = new LineBreaker($nextLine);
        }

        $scale = $this->fontMeasurement->getFontScaling();
        $scaledWidth = $targetWidth * $scale;
        [$line, $width] = $this->lineBreaker->nextLine($scaledWidth, $allowEmpty);

        return new Fragment($line, $this->phrase->getTextStyle(), $width / $scale);
    }
}
