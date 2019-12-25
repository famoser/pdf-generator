<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\File\Structure;

use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\File\Structure\Base\BaseStructure;
use PdfGenerator\Backend\File\StructureVisitor;

class FileTrailer extends BaseStructure
{
    /**
     * @var int
     */
    private $size;

    /**
     * @var int
     */
    private $startOfCrossReferenceTable;

    /**
     * @var BaseObject
     */
    private $root;

    /**
     * FileTrailer constructor.
     */
    public function __construct(int $size, int $startOfCrossReferenceTable, BaseObject $root)
    {
        $this->size = $size;
        $this->startOfCrossReferenceTable = $startOfCrossReferenceTable;
        $this->root = $root;
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
}
