<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\FrontendResources\Allocator\Content;

use PdfGenerator\Frontend\Content\Style\ParagraphStyle;
use PdfGenerator\FrontendResources\Allocator\Content\ParagraphAllocator\ParagraphBreaker;
use PdfGenerator\FrontendResources\MeasuredContent\Paragraph;
use PdfGenerator\FrontendResources\Size;

class ParagraphAllocator implements ContentAllocatorInterface
{
    private readonly ParagraphStyle $style;

    private readonly ParagraphBreaker $paragraphBreaker;

    private bool $firstTime = false;

    public function __construct(private readonly Paragraph $paragraph)
    {
        $this->style = $paragraph->getStyle();

        $this->paragraphBreaker = new ParagraphBreaker($paragraph);
    }

    public function allocate(string $maxWidth, string $maxHeight): array
    {
        $indent = $this->firstTime ? $this->style->getIndent() : 0;
        [$lines, $width, $height] = $this->paragraphBreaker->nextLines($maxWidth, $maxHeight, $indent, $this->firstTime);

        $size = new Size($width, $height);

        return [$lines, $size];
    }

    public function isEmpty(): bool
    {
        return $this->paragraphBreaker->isEmpty();
    }

    public function minimalWidth(): float
    {
        return 0;
    }

    public function widthEstimate(): float
    {
        return $this->style->getIndent() + $this->paragraphBreaker->widthEstimate();
    }
}
