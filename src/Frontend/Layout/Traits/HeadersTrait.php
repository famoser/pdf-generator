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

use PdfGenerator\Frontend\Layout\Parts\Row;

trait HeadersTrait
{
    /**
     * @var Row[]
     */
    private array $headers = [];

    public function addHeader(Row $row): self
    {
        $this->headers[] = $row;

        return $this;
    }

    /**
     * @return Row[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
