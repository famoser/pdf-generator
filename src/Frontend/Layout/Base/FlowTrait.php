<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout\Base;

trait FlowTrait
{
    public const DIRECTION_ROW = 'row';
    public const DIRECTION_COLUMN = 'column';
    private string $direction;

    /**
     * @var float
     *
     * margin to place in between the items
     */
    private float $gap;

    /**
     * @var float[]|null
     *
     * if not null, forces the dimensions in flow direction to the blocks
     */
    private ?array $dimensions;

    public function setDirection(string $direction): self
    {
        $this->direction = $direction;

        return $this;
    }

    public function setGap(float $gap): self
    {
        $this->gap = $gap;

        return $this;
    }

    /**
     * @param float[]|null $dimensions
     */
    public function setDimensions(?array $dimensions): self
    {
        $this->dimensions = $dimensions;

        return $this;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    public function getGap(): float
    {
        return $this->gap;
    }

    public function getDimensions(): ?array
    {
        return $this->dimensions;
    }
}
