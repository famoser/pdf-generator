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
use PdfGenerator\Font\Backend\File\Traits\BinaryTreeSearchableTrait;
use PdfGenerator\Font\Backend\StreamWriter;

/**
 * two-byte encoding format for continuous code ranges with spaces in between.
 */
class Format4 extends Format
{
    /*
     * for numberOfEntries = segCountX2
     */
    use BinaryTreeSearchableTrait;
    /**
     * how many continuous code ranges are provided (segCount) times two
     * calculated: 2 * segCount.
     *
     * @ttf-type uint16
     */
    private int $segCountX2;

    /**
     * end code per the continuous code range.
     *
     * @ttf-type uint16[]
     *
     * @var int[]
     */
    private array $endCodes = [];

    /**
     * padding set to 0.
     *
     * @ttf-type uint16
     */
    private int $reservedPad;

    /**
     * start code per the continuous code range.
     *
     * @ttf-type uint16[]
     *
     * @var int[]
     */
    private array $startCodes = [];

    /**
     * id delta per the continuous code range.
     *
     * @ttf-type uint16[]
     *
     * @var int[]
     */
    private array $idDeltas = [];

    /**
     * id range offset per the continuous code range.
     *
     * @ttf-type uint16[]
     *
     * @var int[]
     */
    private array $idRangeOffsets = [];

    /**
     * entries referenced to for continuous code ranges where the idRangeOffset is not 0.
     *
     * @ttf-type uint16[]
     *
     * @var int[]
     */
    private array $glyphIndexArray = [];

    /**
     * the format of the encoding.
     *
     * @ttf-type uint16
     */
    public function getFormat(): int
    {
        return self::FORMAT_4;
    }

    /**
     * of which size the binary tree is constructed.
     */
    protected function getNumberOfEntries(): int
    {
        return $this->segCountX2;
    }

    public function getSegCountX2(): int
    {
        return $this->segCountX2;
    }

    public function setSegCountX2(int $segCountX2): void
    {
        $this->segCountX2 = $segCountX2;
    }

    /**
     * @return int[]
     */
    public function getEndCodes(): array
    {
        return $this->endCodes;
    }

    /**
     * @param int[] $endCodes
     */
    public function setEndCodes(array $endCodes): void
    {
        $this->endCodes = $endCodes;
    }

    public function getReservedPad(): int
    {
        return $this->reservedPad;
    }

    public function setReservedPad(int $reservedPad): void
    {
        $this->reservedPad = $reservedPad;
    }

    /**
     * @return int[]
     */
    public function getStartCodes(): array
    {
        return $this->startCodes;
    }

    /**
     * @param int[] $startCodes
     */
    public function setStartCodes(array $startCodes): void
    {
        $this->startCodes = $startCodes;
    }

    /**
     * @return int[]
     */
    public function getIdDeltas(): array
    {
        return $this->idDeltas;
    }

    /**
     * @param int[] $idDeltas
     */
    public function setIdDeltas(array $idDeltas): void
    {
        $this->idDeltas = $idDeltas;
    }

    /**
     * @return int[]
     */
    public function getIdRangeOffsets(): array
    {
        return $this->idRangeOffsets;
    }

    /**
     * @param int[] $idRangeOffsets
     */
    public function setIdRangeOffsets(array $idRangeOffsets): void
    {
        $this->idRangeOffsets = $idRangeOffsets;
    }

    /**
     * @return int[]
     */
    public function getGlyphIndexArray(): array
    {
        return $this->glyphIndexArray;
    }

    /**
     * @param int[] $glyphIndexArray
     */
    public function setGlyphIndexArray(array $glyphIndexArray): void
    {
        $this->glyphIndexArray = $glyphIndexArray;
    }

    public function addStartCode(int $startCode): void
    {
        $this->startCodes[] = $startCode;
    }

    public function addEndCode(int $endCode): void
    {
        $this->endCodes[] = $endCode;
    }

    public function addIdDelta(int $idDelta): void
    {
        $this->idDeltas[] = $idDelta;
    }

    public function addIdRangeOffset(int $idRangeOffset): void
    {
        $this->idRangeOffsets[] = $idRangeOffset;
    }

    public function accept(FormatVisitor $formatVisitor, StreamWriter $streamWriter): void
    {
        $formatVisitor->visitFormat4($this, $streamWriter);
    }
}
