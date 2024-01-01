<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Document\Resource\State;

use PdfGenerator\Backend\Structure\Document\Page\State\ColorState;
use PdfGenerator\IR\Document\Content\Common\Color;

class ColorStateRepository
{
    private ?Color $fillColor = null;

    private ?Color $borderColor = null;

    private ?ColorState $activeColorState = null;

    public function setFillColor(?Color $fillColor): void
    {
        if ($fillColor === $this->fillColor) {
            return;
        }

        $this->fillColor = $fillColor;
        $this->activeColorState = null;
    }

    public function setBorderColor(?Color $borderColor): void
    {
        if ($borderColor === $this->borderColor) {
            return;
        }

        $this->borderColor = $borderColor;
        $this->activeColorState = null;
    }

    public function getColorState(): ColorState
    {
        if (null !== $this->activeColorState) {
            return $this->activeColorState;
        }

        $rgbStrokingColour = self::convertToPdfColourSpecificationOrDefault($this->borderColor);
        $rgbNonStrokingColour = self::convertToPdfColourSpecificationOrDefault($this->fillColor);
        $this->activeColorState = new ColorState($rgbStrokingColour, $rgbNonStrokingColour);

        return $this->activeColorState;
    }

    /**
     * @return float[]
     */
    private static function convertToPdfColourSpecificationOrDefault(?Color $color): array
    {
        if (!$color) {
            return [0, 0, 0];
        }

        return [
            self::convertToPdfColourValue($color->getRed()),
            self::convertToPdfColourValue($color->getGreen()),
            self::convertToPdfColourValue($color->getBlue()),
        ];
    }

    private static function convertToPdfColourValue(int $number): float
    {
        return round($number / 255.0, 2);
    }
}
