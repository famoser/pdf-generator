<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Font\Backend\File\Table\HMtx;

class LongHorMetric
{
    /**
     * the width of the character.
     *
     * @ttf-type uint16
     */
    private int $advanceWidth;

    /**
     * the distance left from the previous character.
     *
     * @ttf-type uint16
     */
    private int $leftSideBearing;

    public function getAdvanceWidth(): int
    {
        return $this->advanceWidth;
    }

    public function setAdvanceWidth(int $advanceWidth): void
    {
        $this->advanceWidth = $advanceWidth;
    }

    public function getLeftSideBearing(): int
    {
        return $this->leftSideBearing;
    }

    public function setLeftSideBearing(int $leftSideBearing): void
    {
        $this->leftSideBearing = $leftSideBearing;
    }
}
