<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Block\Base;

use PdfGenerator\Frontend\Block\Style\Base\BlockStyle;
use PdfGenerator\Frontend\BlockVisitor;

abstract class Block
{
    /**
     * @var float[]|null
     */
    private $dimensions;

    public function __construct(array $dimensions = null)
    {
        $this->dimensions = $dimensions;
    }

    abstract public function getStyle(): BlockStyle;

    abstract public function accept(BlockVisitor $blockVisitor);

    /**
     * @return float[]|null
     */
    public function getDimensions(): ?array
    {
        return $this->dimensions;
    }
}
