<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\Content\Text;

readonly class TextLine
{
    /**
     * @param TextSegment[] $segments
     */
    public function __construct(private array $segments, private float $lineHeight, private float $offset, private float $wordSpacing)
    {
    }

    /**
     * @return TextSegment[]
     */
    public function getSegments(): array
    {
        return $this->segments;
    }

    public function getLineHeight(): float
    {
        return $this->lineHeight;
    }

    public function getOffset(): float
    {
        return $this->offset;
    }

    public function getWordSpacing(): float
    {
        return $this->wordSpacing;
    }
}
