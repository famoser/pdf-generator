<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure2\Font;

use PdfGenerator\IR\Structure2\Font;

class EmbeddedFont extends Font
{
    /**
     * @var string
     */
    private $fontPath;

    /**
     * EmbeddedFont constructor.
     *
     * @param string $fontPath
     */
    public function __construct(string $fontPath)
    {
        $this->fontPath = $fontPath;
    }
}