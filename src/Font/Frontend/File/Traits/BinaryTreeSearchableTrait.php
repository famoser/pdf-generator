<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Font\Frontend\File\Traits;

/**
 * repeatedly, the TTF format contains information how to construct a binary tree over some value range in question
 * when using this trait, do specify the entries over which it will be constructed.
 */
trait BinaryTreeSearchableTrait
{
    /**
     * how many entries can be indexed by a binary search tree
     * calculated: (maximum power of 2 <= numberOfEntries)*16.
     *
     * @ttf-type uint16
     */
    private int $searchRange;

    /**
     * how deep the binary search tree will be
     * calculated: log2(maximum power of 2 <= numberOfEntries).
     *
     * @ttf-type uint16
     */
    private int $entrySelector;

    /**
     * how many entries are missed if only binary search tree is looked at
     * calculated: numberOfEntries*16-searchRange; which is equivalent to (numberOfEntries-binaryTreeNodeCount)*16.
     *
     * @ttf-type uint16
     */
    private int $rangeShift;

    public function getSearchRange(): int
    {
        return $this->searchRange;
    }

    public function setSearchRange(int $searchRange): void
    {
        $this->searchRange = $searchRange;
    }

    public function getEntrySelector(): int
    {
        return $this->entrySelector;
    }

    public function setEntrySelector(int $entrySelector): void
    {
        $this->entrySelector = $entrySelector;
    }

    public function getRangeShift(): int
    {
        return $this->rangeShift;
    }

    public function setRangeShift(int $rangeShift): void
    {
        $this->rangeShift = $rangeShift;
    }

    /**
     * of which size the binary tree is constructed.
     */
    abstract protected function getNumberOfEntries(): int;
}
