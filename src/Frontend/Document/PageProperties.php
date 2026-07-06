<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\Document;

use Famoser\PdfGenerator\Frontend\Document\Transition\PageTransitionTrait;

class PageProperties
{
    use PageTransitionTrait;
    use PageDimensionTrait;

    /**
     * @param float[] $pageSize
     * @param float[] $margin
     */
    public function __construct(private readonly array $pageSize = [210, 297], private readonly mixed $margin = [15, 15, 15, 15], private readonly Unit $unit = Unit::MILLIMETERS, private readonly Rotation $rotation = Rotation::Upright)
    {
    }

    /**
     * @return float[]
     */
    public function getPageSize(): array
    {
        return $this->pageSize;
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
