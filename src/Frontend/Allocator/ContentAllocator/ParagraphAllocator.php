<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Allocator\ContentAllocator;

use PdfGenerator\Frontend\Allocator\ContentAllocator\ParagraphAllocator\ParagraphBreaker;
use PdfGenerator\Frontend\Content\Style\ParagraphStyle;
use PdfGenerator\Frontend\MeasuredContent\Paragraph;
use PdfGenerator\Frontend\MeasuredContent\Utils\FontRepository;
use PdfGenerator\Frontend\Position;
use PdfGenerator\Frontend\Size;

class ParagraphAllocator
{
    /**
     * @var Paragraph
     */
    private $paragraph;

    /**
     * @var ParagraphStyle
     */
    private $style;

    /**
     * @var ParagraphBreaker
     */
    private $paragraphBreaker;

    /**
     * @var bool
     */
    private $firstTime = false;

    /**
     * ParagraphAllocator constructor.
     */
    public function __construct(Paragraph $paragraph, ParagraphStyle $style, FontRepository $fontRepository)
    {
        $this->paragraph = $paragraph;
        $this->style = $style;

        $this->paragraphBreaker = new ParagraphBreaker($paragraph, $fontRepository);
    }

    /**
     * @return mixed[]
     */
    public function allocate(string $maxWidth, string $maxHeight): array
    {
        $indent = $this->firstTime ? $this->style->getIndent() : 0;
        $margins = $this->style->getMarginTop() + $this->style->getMarginBottom();
        $targetHeight = $maxHeight - $margins;
        [$lines, $width, $height] = $this->paragraphBreaker->nextLines($maxWidth, $targetHeight, $indent, $this->firstTime);

        $position = new Position(0, $this->style->getMarginTop());
        $size = new Size($width, $height);
        $paragraph = new \PdfGenerator\Frontend\LocatedContent\Paragraph($position, $size, $lines);

        $totalHeight = $height + $margins;

        return [$paragraph, $width, $totalHeight];
    }

    public function isEmpty()
    {
        return $this->paragraphBreaker->isEmpty();
    }
}
