<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

    public function getSegments(): array
    {
        return $this->segments;
    }
}
