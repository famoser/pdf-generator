<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\IR;

class Resources
{
    /**
     * @var Font[]
     */
    private $fonts = [];

    /**
     * @param Font $font
     */
    public function addFont(Font $font)
    {
        $this->fonts[] = $font;
    }
}
