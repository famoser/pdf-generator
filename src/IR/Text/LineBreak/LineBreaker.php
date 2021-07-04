<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Text\LineBreak;

use PdfGenerator\IR\Text\LineBreak\FontSizer\FontSizer;

class LineBreaker
{
    /**
     * @var FontSizer
     */
    private $sizer;

    /**
     * @var string[]
     */
    private $words;

    public function __construct(FontSizer $sizer, string $text)
    {
        $this->sizer = $sizer;
        $this->words = explode(' ', $text);
    }

    public function getIterator(float $width, int $startWordPosition = 0): LineBreakerIterator
    {
        return new LineBreakerIterator($this->sizer, $width, $this->words, $startWordPosition);
    }
}
