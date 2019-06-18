<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Document\Page\Content;

use PdfGenerator\Backend\Catalog\Content;
use PdfGenerator\Backend\Structure\Document\Page\Content\Base\BaseContent;
use PdfGenerator\Backend\Structure\Document\Page\ContentVisitor;
use PdfGenerator\Backend\Structure\Document\Page\State\Base\BaseState;
use PdfGenerator\Backend\Structure\Document\Page\StateCollections\DrawingState;

class RectangleContent extends BaseContent
{
    const PAINTING_MODE_NONE = 0;
    const PAINTING_MODE_STROKE = 1;
    const PAINTING_MODE_FILL = 2;
    const PAINTING_MODE_STROKE_FILL = 3;

    /**
     * @var float
     */
    private $width;

    /**
     * @var float
     */
    private $height;

    /**
     * @var int
     */
    private $paintingMode;

    /**
     * @var DrawingState
     */
    private $color;

    /**
     * @param float $width
     * @param float $height
     * @param int $paintingMode
     * @param DrawingState $color
     */
    public function __construct(float $width, float $height, int $paintingMode, DrawingState $color)
    {
        $this->width = $width;
        $this->height = $height;
        $this->paintingMode = $paintingMode;
        $this->color = $color;
    }

    /**
     * @return float
     */
    public function getWidth(): float
    {
        return $this->width;
    }

    /**
     * @return float
     */
    public function getHeight(): float
    {
        return $this->height;
    }

    /**
     * @return int
     */
    public function getPaintingMode(): int
    {
        return $this->paintingMode;
    }

    /**
     * @return DrawingState
     */
    public function getColor(): DrawingState
    {
        return $this->color;
    }

    /**
     * @return BaseState[]
     */
    public function getInfluentialStates(): array
    {
        return $this->color->getState();
    }

    /**
     * @param ContentVisitor $visitor
     *
     * @return Content
     */
    public function accept(ContentVisitor $visitor): Content
    {
        return $visitor->visitRectangleContent($this);
    }
}
