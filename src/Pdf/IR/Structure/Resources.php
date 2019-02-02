<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\IR\Structure;

class Resources
{
    /**
     * @var int
     */
    private $resourceCounter;

    /**
     * @var Font[]
     */
    private $fonts = [];

    /**
     * @param string $subtype
     * @param string $baseFont
     */
    public function addFont(string $subtype, string $baseFont)
    {
        $identifier = $this->generateIdentifier('F');
        $this->fonts[$identifier] = new Font($subtype, $baseFont, $identifier);
    }

    /**
     * @param string $prefix
     *
     * @return string
     */
    private function generateIdentifier(string $prefix)
    {
        return $prefix . $this->resourceCounter++;
    }
}
