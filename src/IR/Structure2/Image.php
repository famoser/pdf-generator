<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure2;

class Image
{
    /**
     * @var string
     */
    private $imagePath;

    /**
     * Image constructor.
     *
     * @param string $imagePath
     */
    public function __construct(string $imagePath)
    {
        $this->imagePath = $imagePath;
    }
}
