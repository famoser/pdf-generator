<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Document\Page\State;

use PdfGenerator\Backend\Structure\Document\Page\State\ColorState;
use PdfGenerator\IR\Structure\Document\Page\Content\Common\Color;

class ColorStateRepository
{
    private ?Color $fillColor;

    private ?Color $borderColor;

    private ColorState $activeColorState;

    public function setFillColor(?Color $fillColor)
    {
        if ($fillColor === $this->fillColor) {
            return;
        }

        $this->fillColor = $fillColor;
        $this->activeColorState = null;
    }

    public function setBorderColor(?Color $borderColor)
    {
        if ($borderColor === $this->borderColor) {
            return;
        }

        $this->borderColor = $borderColor;
        $this->activeColorState = null;
    }

    private function convertToPdfColourSpecification(Color $color): array
    {
        return [
            $this->convertToPdfColourValue($color->getRed()),
            $this->convertToPdfColourValue($color->getGreen()),
            $this->convertToPdfColourValue($color->getBlue()),
        ];
    }

    private function convertToPdfColourValue(int $number): float
    {
        return round($number / 255.0, 2);
    }

    public function getColorState(): ColorState
    {
        if (null !== $this->activeColorState) {
            return $this->activeColorState;
        }

        $this->activeColorState = new ColorState();

        if (null !== $this->fillColor) {
            $rgbNonStrokingColour = $this->convertToPdfColourSpecification($this->fillColor);
            $this->activeColorState->setRgbNonStrokingColour($rgbNonStrokingColour);
        }

        if (null !== $this->borderColor) {
            $rgbStrokingColour = $this->convertToPdfColourSpecification($this->borderColor);
            $this->activeColorState->setRgbStrokingColour($rgbStrokingColour);
        }

        return $this->activeColorState;
    }
}
