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

use PdfGenerator\Frontend\Content\Style\ParagraphStyle;
use PdfGenerator\Frontend\Cursor;
use PdfGenerator\Frontend\MeasuredContent\Paragraph;
use PdfGenerator\IR\Text\GreedyLineBreaker\ParagraphBreaker;

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
     * ParagraphAllocator constructor.
     */
    public function __construct(Paragraph $paragraph, ParagraphStyle $style)
    {
        $this->paragraph = $paragraph;
        $this->style = $style;

        $this->paragraphBreaker = new ParagraphBreaker($paragraph);
    }

    public function allocate(Cursor $cursor, string $width, string $height)
    {
        $cursor = Cursor::moveRightDown($cursor, 0, $this->style->getMarginTop());
        $lines = $this->paragraphBreaker->nextLines($width, $height, $this->style->getIndent(), false);
    }

    public function isEmpty()
    {
    }
}
