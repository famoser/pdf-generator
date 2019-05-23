<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\IR\Utils\CMap\Format4;

class Segment
{
    /**
     * @var int
     */
    private $startCode;

    /**
     * @var int
     */
    private $endCode;

    /**
     * @var int
     */
    private $idDelta;

    /**
     * @var int
     */
    private $idRangeOffset;

    /**
     * @return int
     */
    public function getStartCode(): int
    {
        return $this->startCode;
    }

    /**
     * @param int $startCode
     */
    public function setStartCode(int $startCode): void
    {
        $this->startCode = $startCode;
    }

    /**
     * @return int
     */
    public function getEndCode(): int
    {
        return $this->endCode;
    }

    /**
     * @param int $endCode
     */
    public function setEndCode(int $endCode): void
    {
        $this->endCode = $endCode;
    }

    /**
     * @return int
     */
    public function getIdDelta(): int
    {
        return $this->idDelta;
    }

    /**
     * @param int $idDelta
     */
    public function setIdDelta(int $idDelta): void
    {
        $this->idDelta = $idDelta;
    }

    /**
     * @return int
     */
    public function getIdRangeOffset(): int
    {
        return $this->idRangeOffset;
    }

    /**
     * @param int $idRangeOffset
     */
    public function setIdRangeOffset(int $idRangeOffset): void
    {
        $this->idRangeOffset = $idRangeOffset;
    }
}
