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
use Famoser\PdfGenerator\Backend\File\Object\DictionaryObject;

abstract readonly class BaseStructure
{
    /**
     * @var array<string, mixed>
     */
    private array $values;

    public function setValue(string $key, mixed $value): void
    {
        $this->values[$key] = $value;
    }

    /**
     * @return array<string, mixed>
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @return BaseObject|BaseObject[]
     */
    abstract public function accept(CatalogVisitor $visitor): BaseObject|array;
}
