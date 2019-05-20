<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\Structure\Table\CMap\Format;

use PdfGenerator\Font\Frontend\Structure\Table\CMap\VisitorInterface;

class Format12 extends Format
{
    /**
     * length.
     *
     * @ttf-type uint32
     */

    /**
     * language.
     *
     * @ttf-type uint32
     */

    /**
     * number of groupings.
     *
     * @ttf-type uint32
     *
     * @return int
     */
    private $nGroups;

    /**
     * @var Format12Group[]
     */
    private $groups = [];

    /**
     * the format of the encoding.
     *
     * @ttf-type fixed32
     *
     * @return int
     */
    public function getFormat(): int
    {
        return self::FORMAT_12;
    }

    /**
     * @param VisitorInterface $formatVisitor
     *
     * @return mixed
     */
    public function accept(VisitorInterface $formatVisitor)
    {
        return $formatVisitor->visitFormat12($this);
    }

    /**
     * @return int
     */
    public function getNGroups(): int
    {
        return $this->nGroups;
    }

    /**
     * @param int $nGroups
     */
    public function setNGroups(int $nGroups): void
    {
        $this->nGroups = $nGroups;
    }

    /**
     * @return Format12Group[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @param Format12Group $group
     */
    public function addGroup(Format12Group $group): void
    {
        $this->groups[] = $group;
    }
}
