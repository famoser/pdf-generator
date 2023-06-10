<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Catalog\Base;

abstract readonly class BaseIdentifiableStructure extends BaseStructure
{
    public function __construct(private string $identifier)
    {
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
