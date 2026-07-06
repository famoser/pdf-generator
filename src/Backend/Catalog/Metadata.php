<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\Catalog;

use Famoser\PdfGenerator\Backend\Catalog\Base\BaseStructure;
use Famoser\PdfGenerator\Backend\CatalogVisitor;
use Famoser\PdfGenerator\Backend\File\Object\Base\BaseObject;

readonly class Metadata extends BaseStructure
{
    public function __construct(private string $xml)
    {
    }

    public function getXml(): string
    {
        return $this->xml;
    }

    public function accept(CatalogVisitor $visitor): BaseObject
    {
        return $visitor->visitMetadata($this);
    }
}
