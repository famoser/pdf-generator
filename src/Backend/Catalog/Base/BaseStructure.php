<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\Catalog\Base;

use Famoser\PdfGenerator\Backend\CatalogVisitor;
use Famoser\PdfGenerator\Backend\File\Object\Base\BaseObject;

abstract readonly class BaseStructure
{
    /**
     * @param array<string, mixed> $objectValues
     */
    public function __construct(private array $objectValues = [])
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function getObjectValues(): array
    {
        return $this->objectValues;
    }

    /**
     * @return BaseObject|BaseObject[]
     */
    abstract public function accept(CatalogVisitor $visitor): BaseObject|array;
}
