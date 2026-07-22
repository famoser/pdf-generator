<?php

namespace Famoser\PdfGenerator\IR\Document\Content\Text;

readonly class TextLine
{
    /**
     * @param TextSegment[] $segments
     */
    public function __construct(private float $offset, private array $segments)
    {
    }

    public function getOffset(): float
    {
        return $this->offset;
    }

    /**
     * @return TextSegment[]
     */
    public function getSegments(): array
    {
        return $this->segments;
    }
}
