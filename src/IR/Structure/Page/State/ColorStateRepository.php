<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Configuration\State;

use PdfGenerator\Backend\Content\Operators\State\ColorState;
use PdfGenerator\IR\Structure\PageContent\Common\Color;

class ColorStateRepository
{
    /**
     * @var Color|null
     */
    private $fillColor = null;

    /**
     * @var Color|null
     */
    private $borderColor = null;

    /**
     * @var ColorState
     */
    private $activeColorState;

    /**
     * @param Color $fillColor
     */
    public function setFillColor(Color $fillColor)
    {
        if ($fillColor === $this->fillColor) {
            return;
        }

        $this->fillColor = $fillColor;
        $this->activeColorState = null;
    }

    /**
     * @param Color $borderColor
     */
    public function setBorderColor(Color $borderColor)
    {
        if ($borderColor === $this->borderColor) {
            return;
        }

        $this->borderColor = $borderColor;
        $this->activeColorState = null;
    }

    /**
     * @param Color $color
     *
     * @return array
     */
    private function convertToPdfColourSpecification(Color $color)
    {
        return [
            $this->convertToPdfColourValue($color->getRed()),
            $this->convertToPdfColourValue($color->getGreen()),
            $this->convertToPdfColourValue($color->getBlue()),
        ];
    }

    /**
     * @param int $number
     *
     * @return float
     */
    private function convertToPdfColourValue(int $number)
    {
        return round($number / 255.0, 2);
    }

    /**
     * @return ColorState
     */
    public function getColorState()
    {
        if ($this->activeColorState !== null) {
            return $this->activeColorState;
        }

        $this->activeColorState = new ColorState();

        if ($this->fillColor !== null) {
            $rgbNonStrokingColour = $this->convertToPdfColourSpecification($this->fillColor);
            $this->activeColorState->setRgbNonStrokingColour($rgbNonStrokingColour);
        }

        if ($this->borderColor !== null) {
            $rgbStrokingColour = $this->convertToPdfColourSpecification($this->borderColor);
            $this->activeColorState->setRgbStrokingColour($rgbStrokingColour);
        }

        return $this->activeColorState;
    }
}
