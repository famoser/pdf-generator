<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Catalog;

use PdfGenerator\Backend\Catalog\Base\BaseIdentifiableStructure;

abstract class Font extends BaseIdentifiableStructure
{
    /**
     * File constructor.
     */
    public function __construct(string $identifier)
    {
        $this->setIdentifier($identifier);
    }

    abstract public function encode(string $value): string;
}
