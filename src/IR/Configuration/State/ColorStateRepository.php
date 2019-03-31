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

class ColorStateRepository
{
    /**
     * @var string|null
     */
    private $fillColor = '#000000';

    /**
     * @var string|null
     */
    private $borderColor = '#000000';

    /**
     * @var ColorState
     */
    private $activeColorState;

    /**
     * @param string $fillColor
     *
     * @throws \Exception
     */
    public function setFillColor(string $fillColor)
    {
        $this->throwIfNoHexColor($fillColor);

        $this->fillColor = $fillColor;

        $this->activeColorState = null;
    }

    /**
     * @param string $borderColor
     *
     * @throws \Exception
     */
    public function setBorderColor(string $borderColor)
    {
        $this->throwIfNoHexColor($borderColor);

        $this->borderColor = $borderColor;

        $this->activeColorState = null;
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
        $this->activeColorState->setRgbNonStrokingColour($this->convertToPdfColourSpecification($this->fillColor));
        $this->activeColorState->setRgbStrokingColour($this->convertToPdfColourSpecification($this->borderColor));

        return $this->activeColorState;
    }

    /**
     * @param string $value
     *
     * @throws \Exception
     */
    private function throwIfNoHexColor(string $value)
    {
        if (!(\is_string($value) && preg_match('/^#([a-f0-9]){6}$/', $value))) {
            throw new \Exception('colors must be specified as a hex value like #000000');
        }
    }

    /**
     * converts a hex color.
     *
     * @param string $value
     *
     * @return array
     */
    private function convertToPdfColourSpecification(string $value)
    {
        list($red, $green, $blue) = sscanf($value, '#%02x%02x%02x');

        return [
            $this->convertToPdfColourValue($red),
            $this->convertToPdfColourValue($green),
            $this->convertToPdfColourValue($blue),
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
}
