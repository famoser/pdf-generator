<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Printer;

class Line
{
    /**
     * @var Fragment[]
     */
    private array $fragments = [];

    private float $ascender;

    private float $descender;

    private float $leading;

    /**
     * Line constructor.
     */
    public function __construct(float $ascender, float $descender, float $leading)
    {
        $this->ascender = $ascender;
        $this->descender = $descender;
        $this->leading = $leading;
    }

    public function addFragment(Fragment $fragment): void
    {
        $this->fragments[] = $fragment;
    }

    public function getAscender(): float
    {
        return $this->ascender;
    }

    public function getDescender(): float
    {
        return $this->descender;
    }

    public function getLeading(): float
    {
        return $this->leading;
    }

    /**
     * @return Fragment[]
     */
    public function getFragments(): array
    {
        return $this->fragments;
    }
}
