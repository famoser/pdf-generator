<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Text\LineBreak\FontSizer;

use PdfGenerator\IR\Structure\Document\Font;

class EmbeddedFontFontSizer implements ResizableFontSizer
{
    /**
     * @var Font
     */
    private $font;

    /**
     * @var int[]
     */
    private $characterAdvanceWidthLookup = [];

    /**
     * @var int
     */
    private $invalidCharacterWidth;

    /**
     * @var int
     */
    private $spaceCharacterWidth;

    /**
     * @var float
     */
    private $scale = 1;

    public function __construct(Font\EmbeddedFont $font)
    {
        $this->font = $font->getFont();

        $characters = array_merge($this->font->getReservedCharacters(), $this->font->getCharacters());
        foreach ($characters as $character) {
            $this->characterAdvanceWidthLookup[$character->getUnicodePoint()] = $character->getLongHorMetric()->getAdvanceWidth();
        }

        $this->invalidCharacterWidth = $font->getFont()->getReservedCharacters()[0]->getLongHorMetric()->getAdvanceWidth();
        $this->spaceCharacterWidth = $this->getWidth(' ');
    }

    public function setFontSize(float $fontSize)
    {
        $this->scale = 1024 / $this->font->getTableDirectory()->getHeadTable()->getUnitsPerEm() * $fontSize;
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

    /**
     * top of text area until baseline.
     */
    public function getAscender()
    {
        return $this->font->getTableDirectory()->getOS2Table()->getSTypoAscender();
    }

    /**
     * bottom of text area until baseline
     * negative, as measured "the other way around".
     */
    public function getDescender()
    {
        return $this->font->getTableDirectory()->getOS2Table()->getSTypoDecender();
    }

    /**
     * Gap between two text areas below each others.
     */
    public function getLineGap()
    {
        return $this->font->getTableDirectory()->getOS2Table()->getSTypoLineGap();
    }

    public function getBaselineToBaselineDistance()
    {
        return $this->getAscender() - $this->getDescender() + $this->getLineGap();
    }
}
