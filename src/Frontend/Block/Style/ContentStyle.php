<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Block\Style;

use PdfGenerator\Frontend\Block\Style\Base\BlockStyle;

class ContentStyle extends BlockStyle
{
    final public const FLOAT_TOP_LEFT = 'FLOAT_TOP_LEFT';
    final public const FLOAT_TOP_RIGHT = 'FLOAT_TOP_RIGHT';
    final public const FLOAT_BOTTOM_LEFT = 'FLOAT_BOTTOM_LEFT';
    final public const FLOAT_BOTTOM_RIGHT = 'FLOAT_BUTTOM_RIGHT';

    public function __construct(private readonly ?string $float = null)
    {
        parent::__construct();
    }

    public function getFloat(): ?string
    {
        return $this->float;
    }
}
