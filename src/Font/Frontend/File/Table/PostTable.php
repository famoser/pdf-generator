<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\File\Table;

use PdfGenerator\Font\Frontend\File\Table\Post\Format\Format;

/**
 * the post script table includes information needed by postscript printers.
 * is required by ttf files, but probably not useful for fonts used within pdf.
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6post.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/post
 */
class PostTable
{
    /**
     * the version of the PostScript information.
     *
     * @ttf-type fixed
     *
     * @var float
     */
    private $version;

    /**
     * the italic angle of the characters in degrees.
     * 0 for upwards position
     * negative if leans to the right.
     *
     * @ttf-type fixed
     *
     * @var float
     */
    private $italicAngle;

    /**
     * distance between top of underline until baseline.
     *
     * @ttf-type FWORD
     *
     * @var int
     */
    private $underlinePosition;

    /**
     * thickness of underline character.
     *
     * @ttf-type FWORD
     *
     * @var int
     */
    private $underlineThickness;

    /**
     * spacing information
     * 0 for proportionally spaced
     * other value for monospaced.
     *
     * @ttf-type uint32
     *
     * @var int
     */
    private $isFixedPitch;

    /**
     * minimum memory usage for download.
     *
     * @ttf-type uint32
     *
     * @var int
     */
    private $minMemType42;

    /**
     * maximum memory usage for download.
     *
     * @ttf-type uint32
     *
     * @var int
     */
    private $maxMemType42;

    /**
     * minimum memory usage for download as type 1.
     *
     * @ttf-type uint32
     *
     * @var int
     */
    private $minMemType1;

    /**
     * maximum memory usage for download as type 1.
     *
     * @ttf-type uint32
     *
     * @var int
     */
    private $maxMemType1;

    /**
     * @var Format
     */
    private $format;

    public function getVersion(): float
    {
        return $this->version;
    }

    public function setVersion(float $version): void
    {
        $this->version = $version;
    }

    public function getItalicAngle(): float
    {
        return $this->italicAngle;
    }

    public function setItalicAngle(float $italicAngle): void
    {
        $this->italicAngle = $italicAngle;
    }

    public function getUnderlinePosition(): int
    {
        return $this->underlinePosition;
    }

    public function setUnderlinePosition(int $underlinePosition): void
    {
        $this->underlinePosition = $underlinePosition;
    }

    public function getUnderlineThickness(): int
    {
        return $this->underlineThickness;
    }

    public function setUnderlineThickness(int $underlineThickness): void
    {
        $this->underlineThickness = $underlineThickness;
    }

    public function getIsFixedPitch(): int
    {
        return $this->isFixedPitch;
    }

    public function setIsFixedPitch(int $isFixedPitch): void
    {
        $this->isFixedPitch = $isFixedPitch;
    }

    public function getMinMemType42(): int
    {
        return $this->minMemType42;
    }

    public function setMinMemType42(int $minMemType42): void
    {
        $this->minMemType42 = $minMemType42;
    }

    public function getMaxMemType42(): int
    {
        return $this->maxMemType42;
    }

    public function setMaxMemType42(int $maxMemType42): void
    {
        $this->maxMemType42 = $maxMemType42;
    }

    public function getMinMemType1(): int
    {
        return $this->minMemType1;
    }

    public function setMinMemType1(int $minMemType1): void
    {
        $this->minMemType1 = $minMemType1;
    }

    public function getMaxMemType1(): int
    {
        return $this->maxMemType1;
    }

    public function setMaxMemType1(int $maxMemType1): void
    {
        $this->maxMemType1 = $maxMemType1;
    }

    public function getFormat(): Format
    {
        return $this->format;
    }

    public function setFormat(Format $format): void
    {
        $this->format = $format;
    }
}
