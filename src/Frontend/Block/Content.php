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
use PdfGenerator\Frontend\BlockVisitor;

class Content extends Block
{
    /**
     * @var BlockStyle
     */
    private $style;

    /**
     * Content constructor.
     */
    public function __construct(BlockStyle $style, array $dimensions = null)
    {
        parent::__construct($dimensions);

        $this->style = $style;
    }

    public function getStyle(): BlockStyle
    {
        return $this->style;
    }

    public function accept(BlockVisitor $blockVisitor)
    {
        // TODO: Implement accept() method.
    }
}
