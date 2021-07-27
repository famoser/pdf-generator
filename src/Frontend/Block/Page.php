<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Block;

use PdfGenerator\Frontend\Block\Base\Block;
use PdfGenerator\Frontend\Block\Style\Base\BlockStyle;
use PdfGenerator\Frontend\Block\Style\PageStyle;

class Page extends Block
{
    /**
     * @var PageStyle
     */
    private $style;

    /**
     * Page constructor.
     *
     * @param float[] $dimensions
     */
    public function __construct(PageStyle $style = null, array $dimensions = [210, 297])
    {
        parent::__construct($dimensions);

        $this->style = $style ?? new PageStyle();
    }

    public function getStyle(): BlockStyle
    {
        return $this->style;
    }
}
