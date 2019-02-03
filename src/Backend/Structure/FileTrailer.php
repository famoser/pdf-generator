<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure;

use PdfGenerator\Backend\Object\Base\BaseObject;
use PdfGenerator\Backend\Structure\Base\BaseStructure;
use PdfGenerator\Backend\StructureVisitor;

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
     *
     * @param int $size
     * @param int $startOfCrossReferenceTable
     * @param BaseObject $root
     */
    public function __construct(int $size, int $startOfCrossReferenceTable, BaseObject $root)
    {
        $this->size = $size;
        $this->startOfCrossReferenceTable = $startOfCrossReferenceTable;
        $this->root = $root;
    }

    /**
     * @param StructureVisitor $visitor
     *
     * @return string
     */
    public function accept(StructureVisitor $visitor): string
    {
        return $visitor->visitFileTrailer($this);
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return int
     */
    public function getStartOfCrossReferenceTable(): int
    {
        return $this->startOfCrossReferenceTable;
    }

    /**
     * @return BaseObject
     */
    public function getRoot(): BaseObject
    {
        return $this->root;
    }
}
