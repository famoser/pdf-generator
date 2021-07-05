<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Text\LineBreak\WordSizer;

class MonospaceWordSizer
{
    /**
     * @var int
     */
    private $characterWidth;

    public function __construct(int $characterWidth)
    {
        $this->characterWidth = $characterWidth;
    }

    public function getWidth(string $word): float
    {
        return mb_strlen($word) * $this->characterWidth;
    }

    public function getSpaceWidth(): float
    {
        return $this->characterWidth;
    }
}
