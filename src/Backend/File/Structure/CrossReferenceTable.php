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

use Famoser\PdfGenerator\Backend\File\Structure\Base\BaseStructure;
use Famoser\PdfGenerator\Backend\File\StructureVisitor;

class CrossReferenceTable extends BaseStructure
{
    private int $lastEntry = 0;

    /**
     * @var int[]
     */
    private array $registeredEntries = [];

    public function registerEntrySize(int $entrySize): void
    {
        $this->lastEntry += $entrySize;
        $this->registeredEntries[] = $this->lastEntry;
    }

    /**
     * @param int[] $entrySizes
     */
    public function registerEntrySizes(array $entrySizes): void
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

    /**
     * @return int[]
     */
    public function getEntries(): array
    {
        $entries = $this->registeredEntries;
        array_pop($entries);

        return $entries;
    }
}
