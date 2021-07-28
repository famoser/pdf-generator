<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\MeasuredContent;

use PdfGenerator\Frontend\MeasuredContent\Base\MeasuredContent;

class Image extends MeasuredContent
{
    /**
     * @var \PdfGenerator\IR\Structure\Document\Image
     */
    private $image;

    /**
     * Image constructor.
     */
    public function __construct(\PdfGenerator\IR\Structure\Document\Image $image)
    {
        $this->image = $image;
    }

    public function getImage(): \PdfGenerator\IR\Structure\Document\Image
    {
        return $this->image;
    }

    public function getWidth(): float
    {
        return $this->image->getWidth();
    }
}
