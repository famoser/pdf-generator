<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Buffer\RowBuffer;

class MeasuredRow
{
    /**
     * @var MeasuredColumn[]
     */
    private array $measuredColumns = [];

    public function addMeasuredColumn(MeasuredColumn $column): void
    {
        $this->measuredColumns[] = $column;
    }

    /**
     * @return MeasuredColumn[]
     */
    public function getMeasuredColumns(): array
    {
        return $this->measuredColumns;
    }
}
