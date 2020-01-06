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

use PdfGenerator\Backend\File\Structure\Base\BaseStructure;
use PdfGenerator\Backend\File\StructureVisitor;

class CrossReferenceTable extends BaseStructure
{
    /**
     * @var int
     */
    private $lastEntry = 0;

    /**
     * @var int[]
     */
    private $registeredEntries = [];

    public function registerEntrySize(int $entrySize)
    {
        $this->lastEntry += $entrySize;
        $this->registeredEntries[] = $this->lastEntry;
    }

    /**
     * @param int[] $entrySizes
     */
    public function registerEntrySizes(array $entrySizes)
    {
        foreach ($entrySizes as $entrySize) {
            $this->registerEntrySize($entrySize);
        }
    }

    public function accept(StructureVisitor $visitor): string
    {
        return $visitor->visitCrossReferenceTable($this);
    }

    public function getLastEntry(): int
    {
        return $this->lastEntry;
    }

    public function getEntries(): array
    {
        $entries = $this->registeredEntries;
        array_pop($entries);

        return $entries;
    }
}
