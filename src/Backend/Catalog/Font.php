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

use PdfGenerator\Backend\Catalog\Base\BaseStructure;
use PdfGenerator\Backend\Catalog\Base\IdentifiableStructureTrait;
use PdfGenerator\Backend\File\File;

abstract class Font extends BaseStructure
{
    use IdentifiableStructureTrait;

    /**
     * File constructor.
     *
     * @param string $identifier
     */
    public function __construct(string $identifier)
    {
        $this->setIdentifier($identifier);
    }
}
