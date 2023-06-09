<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Buffer;

use PdfGenerator\IR\Buffer\RowBuffer\MeasuredRow;

class TableBuffer
{
    /**
     * @var MeasuredRow[]
     */
    private array $rows = [];

    public function add(RowBuffer $buffer): void
    {
        $this->rows[] = $buffer->getRow();
    }

    /**
     * @return MeasuredRow[]
     */
    public function getRows(): array
    {
        return $this->rows;
    }
}
