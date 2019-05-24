<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Backend\File\Table;

use PdfGenerator\Font\Backend\File\Table\Base\BaseTable;
use PdfGenerator\Font\Backend\File\Table\Post\Format\Format2;
use PdfGenerator\Font\Backend\File\TableVisitor;

/**
 * the post script table includes information needed by postscript printers.
 * is required by ttf files, but probably not useful for fonts used within pdf.
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6post.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/post
 */
class PostTable extends BaseTable
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
     * @var Format2
     */
    private $format;

    /**
     * @return float
     */
    public function getVersion(): float
    {
        return $this->version;
    }

    /**
     * @param float $version
     */
    public function setVersion(float $version): void
    {
        $this->version = $version;
    }

    /**
     * @return float
     */
    public function getItalicAngle(): float
    {
        return $this->italicAngle;
    }

    /**
     * @param float $italicAngle
     */
    public function setItalicAngle(float $italicAngle): void
    {
        $this->italicAngle = $italicAngle;
    }

    /**
     * @return int
     */
    public function getUnderlinePosition(): int
    {
        return $this->underlinePosition;
    }

    /**
     * @param int $underlinePosition
     */
    public function setUnderlinePosition(int $underlinePosition): void
    {
        $this->underlinePosition = $underlinePosition;
    }

    /**
     * @return int
     */
    public function getUnderlineThickness(): int
    {
        return $this->underlineThickness;
    }

    /**
     * @param int $underlineThickness
     */
    public function setUnderlineThickness(int $underlineThickness): void
    {
        $this->underlineThickness = $underlineThickness;
    }

    /**
     * @return int
     */
    public function getIsFixedPitch(): int
    {
        return $this->isFixedPitch;
    }

    /**
     * @param int $isFixedPitch
     */
    public function setIsFixedPitch(int $isFixedPitch): void
    {
        $this->isFixedPitch = $isFixedPitch;
    }

    /**
     * @return int
     */
    public function getMinMemType42(): int
    {
        return $this->minMemType42;
    }

    /**
     * @param int $minMemType42
     */
    public function setMinMemType42(int $minMemType42): void
    {
        $this->minMemType42 = $minMemType42;
    }

    /**
     * @return int
     */
    public function getMaxMemType42(): int
    {
        return $this->maxMemType42;
    }

    /**
     * @param int $maxMemType42
     */
    public function setMaxMemType42(int $maxMemType42): void
    {
        $this->maxMemType42 = $maxMemType42;
    }

    /**
     * @return int
     */
    public function getMinMemType1(): int
    {
        return $this->minMemType1;
    }

    /**
     * @param int $minMemType1
     */
    public function setMinMemType1(int $minMemType1): void
    {
        $this->minMemType1 = $minMemType1;
    }

    /**
     * @return int
     */
    public function getMaxMemType1(): int
    {
        return $this->maxMemType1;
    }

    /**
     * @param int $maxMemType1
     */
    public function setMaxMemType1(int $maxMemType1): void
    {
        $this->maxMemType1 = $maxMemType1;
    }

    /**
     * @return Format2
     */
    public function getFormat(): Format2
    {
        return $this->format;
    }

    /**
     * @param Format2 $format
     */
    public function setFormat(Format2 $format): void
    {
        $this->format = $format;
    }

    /**
     * @param TableVisitor $visitor
     *
     * @return string
     */
    public function accept(TableVisitor $visitor): string
    {
        return $visitor->visitPostTable($this);
    }
}
