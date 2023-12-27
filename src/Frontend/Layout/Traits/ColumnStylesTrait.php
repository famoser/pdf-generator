<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout\Traits;

use PdfGenerator\Frontend\Layout\Style\ColumnStyle;

trait ColumnStylesTrait
{
    /**
     * @var ColumnStyle[]
     */
    private array $columnStyles = [];

    public function setColumnStyle(int $index, ColumnStyle $columnStyle): self
    {
        $this->columnStyles[$index] = $columnStyle;

        return $this;
    }

    /**
     * @return ColumnStyle[]
     */
    public function getColumnStyles(): array
    {
        return $this->columnStyles;
    }
}
