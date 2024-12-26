<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\Resource\Font\WordSizer;

readonly class ProportionalWordSizer implements WordSizerInterface
{
    private int $spaceCharacterWidth;

    /**
     * @param int[] $characterAdvanceWidthLookup
     */
    public function __construct(private int $invalidCharacterWidth, private array $characterAdvanceWidthLookup)
    {
        $this->spaceCharacterWidth = $this->getWidth(' ');
    }

    public function getWidth(string $word): int
    {
        if ('' === $word) {
            return 0;
        }

        $characters = preg_split('//u', $word, -1, \PREG_SPLIT_NO_EMPTY);
        $width = 0;
        foreach ($characters as $character) {
            $codepoint = mb_ord($character, 'UTF-8');
            if (\array_key_exists($codepoint, $this->characterAdvanceWidthLookup)) {
                $width += $this->characterAdvanceWidthLookup[$codepoint];
            } else {
                $width += $this->invalidCharacterWidth;
            }
        }

        return $width;
    }

    public function getSpaceWidth(): int
    {
        return $this->spaceCharacterWidth;
    }
}
