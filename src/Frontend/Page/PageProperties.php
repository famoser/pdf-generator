<?php

namespace Famoser\PdfGenerator\Frontend\Page;

use Famoser\PdfGenerator\Frontend\Page\Transition\TransitionTrait;

class PageProperties
{
    use TransitionTrait;
    use DimensionTrait;

    /**
     * @param float[] $size
     * @param float[] $margin
     */
    public function __construct(private readonly array $size = [210, 297], private readonly mixed $margin = [15, 15, 15, 15], private readonly Unit $unit = Unit::MILLIMETERS, private readonly Rotation $rotation = Rotation::Upright)
    {
    }

    /**
     * @return float[]
     */
    public function getSize(): array
    {
        return $this->size;
    }

    /**
     * @return float[]
     */
    public function getMargin(): array
    {
        return $this->margin;
    }

    public function getRotation(): Rotation
    {
        return $this->rotation;
    }

    public function getUnit(): Unit
    {
        return $this->unit;
    }
}
