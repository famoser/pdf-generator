<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\Backend\Structure;

use Pdf\Backend\Structure\Base\BaseStructure;
use Pdf\Backend\StructureVisitor;

class CrossReferenceTable extends BaseStructure
{
    /**
     * @var int
     */
    private $lastEntry = 0;

    /**
     * @var int[]
     */
    private $entries = [];

    /**
     * @param int $entrySize
     */
    public function registerEntrySize(int $entrySize)
    {
        $this->lastEntry += $entrySize;
        $this->entries[] = $this->lastEntry;
    }

    /**
     * @param StructureVisitor $visitor
     *
     * @return string
     */
    public function accept(StructureVisitor $visitor): string
    {
        return $visitor->visitCrossReferenceTable($this);
    }

    /**
     * @return int
     */
    public function getLastEntry(): int
    {
        return $this->lastEntry;
    }

    /**
     * @return int[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }
}
