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
    private static int $nextIdentifier = 0;

    private ?int $identifier;

    public function getIdentifier(): int
    {
        if (null === $this->identifier) {
            $this->identifier = self::$nextIdentifier++;
        }

        return $this->identifier;
    }
}
