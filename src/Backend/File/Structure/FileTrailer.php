<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\File\Structure;

use Famoser\PdfGenerator\Backend\File\Object\Base\BaseObject;
use Famoser\PdfGenerator\Backend\File\Structure\Base\BaseStructure;
use Famoser\PdfGenerator\Backend\File\StructureVisitor;

class FileTrailer extends BaseStructure
{
    public function __construct(private readonly int $size, private readonly int $startOfCrossReferenceTable, private readonly BaseObject $root, private readonly BaseObject $info)
    {
    }

    public function accept(StructureVisitor $visitor): string
    {
        return $visitor->visitFileTrailer($this);
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getStartOfCrossReferenceTable(): int
    {
        return $this->startOfCrossReferenceTable;
    }

    public function getRoot(): BaseObject
    {
        return $this->root;
    }

    public function getInfo(): BaseObject
    {
        return $this->info;
    }
}
