<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\Structure\Table\CMap\Format;

use PdfGenerator\Font\Frontend\Structure\Table\CMap\VisitorInterface;

abstract class Format
{
    const FORMAT_4 = 4;
    const FORMAT_6 = 6;
    const FORMAT_12 = 12;

    /**
     * length of subtable in bytes.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $length;

    /**
     * language (only relevant if used macintosh encoding).
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $language;

    /**
     * the format of the encoding.
     *
     * @ttf-type uint16
     *
     * @return int
     */
    abstract public function getFormat(): int;

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @param int $length
     */
    public function setLength(int $length): void
    {
        $this->length = $length;
    }

    /**
     * @return int
     */
    public function getLanguage(): int
    {
        return $this->language;
    }

    /**
     * @param int $language
     */
    public function setLanguage(int $language): void
    {
        $this->language = $language;
    }

    /**
     * @param VisitorInterface $formatVisitor
     *
     * @return mixed
     */
    abstract public function accept(VisitorInterface $formatVisitor);
}
