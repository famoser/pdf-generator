<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Style\Part;

class Color
{
    /**
     * @var int
     */
    private $red;

    /**
     * @var int
     */
    private $green;

    /**
     * @var int
     */
    private $blue;

    /**
     * Color constructor.
     */
    public function __construct(int $red, int $green, int $blue)
    {
        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;
    }

    public static function createFromHex(string $color): self
    {
        if (!preg_match('/^#([a-f0-9]){6}$/', $color)) {
            throw new \Exception('please pass the value in the form #000000');
        }

        $rgbArray = sscanf($color, '#%02x%02x%02x');

        return new self($rgbArray[0], $rgbArray[1], $rgbArray[2]);
    }

    public static function black()
    {
        return self::createFromHex('#000000');
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