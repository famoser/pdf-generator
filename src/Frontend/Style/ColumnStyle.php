<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Style;

use PdfGenerator\Frontend\Style\Base\BlockStyle;

class ColumnStyle extends BlockStyle
{
    /**
     * @var float
     */
    private $gutter;

    public function __construct(float $gutter = 0)
    {
        parent::__construct();

        $this->gutter = $gutter;
    }

    /**
     * @return float
     */
    public function getGutter()
    {
        return $this->gutter;
    }
}
