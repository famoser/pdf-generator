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
     */
    abstract public function getFormat(): int;

    public function getLength(): int
    {
        return $this->length;
    }

    public function setLength(int $length): void
    {
        $this->length = $length;
    }

    public function getLanguage(): int
    {
        return $this->language;
    }

    public function setLanguage(int $language): void
    {
        $this->language = $language;
    }

    abstract public function accept(FormatVisitor $formatVisitor, StreamWriter $streamWriter): void;
}
