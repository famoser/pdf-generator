<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Document\Content\Common;

readonly class Color
{
    public function __construct(private int $red, private int $green, private int $blue)
    {
    }

    /**
     * @throws \Exception
     */
    public static function createFromHex(string $color): Color
    {
        if (!preg_match('/^#([a-f0-9]){6}$/', $color)) {
            throw new \Exception('please pass the value in the form #000000');
        }

        $rgbArray = sscanf($color, '#%02x%02x%02x');

        return new self($rgbArray[0], $rgbArray[1], $rgbArray[2]);
    }

    public function getRed(): int
    {
        return $this->red;
    }

    public function getGreen(): int
    {
        return $this->green;
    }

    public function getBlue(): int
    {
        return $this->blue;
    }
}
