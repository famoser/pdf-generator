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

class WordSizer
{
    /**
     * @var int
     */
    private $invalidCharacterWidth;

    /**
     * @var int[]
     */
    private $characterAdvanceWidthLookup;

    /**
     * @var int
     */
    private $spaceCharacterWidth;

    /**
     * @var float
     */
    private $scale = 1;

    public function __construct(int $invalidCharacterWidth, array $characterAdvanceWidthLookup)
    {
        $this->invalidCharacterWidth = $invalidCharacterWidth;
        $this->characterAdvanceWidthLookup = $characterAdvanceWidthLookup;
        $this->spaceCharacterWidth = $this->getWidth(' ');
    }

    public function getWidth(string $word): float
    {
        if ($word === '') {
            return 0;
        }

        $characters = preg_split('//u', $word, -1, \PREG_SPLIT_NO_EMPTY);
        $width = 0;
        foreach ($characters as $character) {
            $codepoint = mb_ord($character, 'UTF-8');
            if (\array_key_exists($codepoint, $this->characterAdvanceWidthLookup)) {
                $width += $this->characterAdvanceWidthLookup[$codepoint] * $this->scale;
            } else {
                $width += $this->invalidCharacterWidth * $this->scale;
            }
        }

        return $width;
    }

    public function getSpaceWidth(): float
    {
        return $this->spaceCharacterWidth * $this->scale;
    }
}
