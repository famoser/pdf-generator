<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Utils;

trait IdentifiableTrait
{
    /**
     * @var int
     */
    private static $nextIdentifier = 0;

    /**
     * @var int|null
     */
    private $identifier = null;

    /**
     * @return int
     */
    public function getIdentifier(): int
    {
        if ($this->identifier === null) {
            $this->identifier = self::$nextIdentifier++;
        }

        return $this->identifier;
    }
}
