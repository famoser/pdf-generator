<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Content\Style;

use PdfGenerator\Frontend\Content\Style\Base\Style;

class ImageStyle extends Style
{
    public const SIZE_CONTAIN = 'SIZING_CONTAIN';

    /**
     * @var string
     */
    private $size;

    /**
     * ImageStyle constructor.
     */
    public function __construct(string $size = self::SIZE_CONTAIN)
    {
        $this->size = $size;
    }

    public function getSize(): string
    {
        return $this->size;
    }
}
