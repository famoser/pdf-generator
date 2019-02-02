<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\Backend\Object\Base;

use Pdf\Backend\ObjectVisitor;

abstract class BaseObject
{
    /**
     * @var int
     */
    private $number;

    /**
     * BaseObject constructor.
     *
     * @param int $number
     */
    public function __construct(int $number)
    {
        $this->number = $number;
    }

    /**
     * @param ObjectVisitor $visitor
     *
     * @return string
     */
    abstract public function accept(ObjectVisitor $visitor): string;

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }
}
