<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\LocatedContent\Paragraph;

class Line
{
    /**
     * @var float
     */
    private $height;

    /**
     * @var float
     */
    private $width;

    /**
     * @var Fragment[]
     */
    private $fragments;

    /**
     * Line constructor.
     */
    public function __construct(float $height)
    {
        $this->height = $height;
    }

    public function addFragment(Fragment $fragment)
    {
        $this->fragments[] = $fragment;
        $this->width += $fragment->getWidth();
    }

    /**
     * @return Fragment[]
     */
    public function getFragments(): array
    {
        return $this->fragments;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function getWidth(): float
    {
        return $this->width;
    }
}
