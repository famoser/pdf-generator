<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Font\Frontend\File\Table\Name;

/**
 * a language tag conforming to IETF BCP 47 https://tools.ietf.org/html/bcp47
 * encoded in UTF-16BE.
 */
class LangTagRecord
{
    /**
     * the length of the language string.
     *
     * @ttf-type uint16
     */
    private int $length;

    /**
     * language tag string offset from beginning of storage area.
     *
     * @ttf-type uint16
     */
    private int $offset;

    /**
     * the actual read out value.
     */
    private string $value;

    public function getLength(): int
    {
        return $this->length;
    }

    public function setLength(int $length): void
    {
        $this->length = $length;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
