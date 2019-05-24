<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Backend\File\Table\CMap\Format;

use PdfGenerator\Font\Backend\File\Table\CMap\FormatVisitor;
use PdfGenerator\Font\Backend\StreamWriter;

abstract class Format
{
    const FORMAT_4 = 4;

    /**
     * length of subtable in bytes.
     *
     * @ttf-type uint16|uint32
     *
     * @var int
     */
    private $length;

    /**
     * language (only relevant if used macintosh encoding).
     *
     * @ttf-type uint16|uint32
     *
     * @var int
     */
    private $language;

    /**
     * the format of the encoding.
     *
     * @ttf-type uint16|fixed32
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
     * @param FormatVisitor $formatVisitor
     * @param StreamWriter $streamWriter
     */
    abstract public function accept(FormatVisitor $formatVisitor, StreamWriter $streamWriter): void;
}
