<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\FrontendResources\MeasuredContent;

use PdfGenerator\Frontend\Content\Style\ParagraphStyle;
use PdfGenerator\FrontendResources\Allocator\Content\ContentAllocatorInterface;
use PdfGenerator\FrontendResources\Allocator\Content\ParagraphAllocator;
use PdfGenerator\FrontendResources\MeasuredContent\Base\MeasuredContent;
use PdfGenerator\FrontendResources\MeasuredContent\Paragraph\Phrase;

class Paragraph extends MeasuredContent
{
    /**
     * @var Phrase[]
     */
    private array $phrases = [];

    public function __construct(private readonly ParagraphStyle $style)
    {
    }

    public function addPhrase(Phrase $phrase): void
    {
        $this->phrases[] = $phrase;
    }

    /**
     * @return Phrase[]
     */
    public function getPhrases(): array
    {
        return $this->phrases;
    }

    public function getWidth(): float
    {
        $maxWidth = 0;
        foreach ($this->phrases as $measuredPhrase) {
            foreach ($measuredPhrase->getMeasuredLines() as $measuredLine) {
                $maxWidth = max($maxWidth, $measuredLine->getWidth());
            }
        }

        return $maxWidth;
    }

    public function getStyle(): ParagraphStyle
    {
        return $this->style;
    }

    public function createAllocator(): ContentAllocatorInterface
    {
        return new ParagraphAllocator($this);
    }
}
