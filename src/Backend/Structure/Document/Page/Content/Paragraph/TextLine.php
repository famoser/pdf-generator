<?php

namespace Famoser\PdfGenerator\Backend\Structure\Document\Page\Content\Paragraph;

readonly class TextLine
{
    /**
     * @param TextSegment[] $segments
     */
    public function __construct(private array $segments, private float $offset)
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
