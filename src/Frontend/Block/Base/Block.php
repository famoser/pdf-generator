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

use PdfGenerator\Frontend\Allocator\AllocatorInterface;
use PdfGenerator\Frontend\Block\Style\Base\BlockStyle;

abstract class Block
{
    /**
     * @var float[]|null
     */
    private ?array $dimensions;

    public function __construct(array $dimensions = null)
    {
        $this->dimensions = $dimensions;
    }

    abstract public function getStyle(): BlockStyle;

    abstract public function createAllocator(): AllocatorInterface;

    /**
     * @return float[]|null
     */
    public function getDimensions(): ?array
    {
        return $this->dimensions;
    }
}
