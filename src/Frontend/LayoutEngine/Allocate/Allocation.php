<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate;

use Famoser\PdfGenerator\Frontend\Content\AbstractContent;
use Famoser\PdfGenerator\Frontend\Layout\AbstractElement;

readonly class Allocation
{
    /**
     * @param Allocation[]   $blockAllocations
     * @param AbstractContent[] $content
     */
    public function __construct(private float $left, private float $top, private float $width, private float $height, private array $blockAllocations = [], private array $content = [], private bool $allocationOverflows = false, private ?AbstractElement $overflow = null)
    {
    }

    public static function shift(Allocation $allocation, float $width, float $height): self
    {
        return new self(
            $allocation->left + $width,
            $allocation->top + $height,
            $allocation->getWidth(),
            $allocation->getHeight(),
            $allocation->getBlockAllocations(),
            $allocation->getContent(),
            $allocation->getAllocationOverflows(),
            $allocation->getOverflow()
        );
    }

    public function getLeft(): float
    {
        return $this->left;
    }

    public function getTop(): float
    {
        return $this->top;
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    /**
     * @return Allocation[]
     */
    public function getBlockAllocations(): array
    {
        return $this->blockAllocations;
    }

    /**
     * @return AbstractContent[]
     */
    public function getContent(): array
    {
        return $this->content;
    }

    public function getAllocationOverflows(): bool
    {
        return $this->allocationOverflows;
    }

    public function getOverflow(): ?AbstractElement
    {
        return $this->overflow;
    }
}
