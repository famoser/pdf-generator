<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Allocator;

use PdfGenerator\Frontend\Cursor;
use PdfGenerator\Frontend\MeasuredContent\Paragraph;

class ContentAllocator
{
    private $content;

    public function allocate(Cursor $start, float $width, float $height)
    {
    }

    public function visitParagraph(Paragraph $paragraph): \PdfGenerator\Frontend\LocatedContent\Paragraph
    {
    }
}
