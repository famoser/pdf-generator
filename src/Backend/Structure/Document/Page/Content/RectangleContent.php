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

readonly class RectangleContent extends BaseContent
{
    final public const PAINTING_MODE_NONE = 0;
    final public const PAINTING_MODE_STROKE = 1;
    final public const PAINTING_MODE_FILL = 2;
    final public const PAINTING_MODE_STROKE_FILL = 3;

    public function __construct(private float $width, private float $height, private int $paintingMode, private DrawingState $drawingState)
    {
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function getPaintingMode(): int
    {
        return $this->paintingMode;
    }

    /**
     * @return BaseState[]
     */
    public function getInfluentialStates(): array
    {
        return $this->drawingState->getState();
    }

    public function accept(ContentVisitor $visitor): Content
    {
        return $visitor->visitRectangleContent($this);
    }

    public function getCurrentTransformationMatrix(): array
    {
        return $this->drawingState->getGeneralGraphicsState()->getCurrentTransformationMatrix();
    }
}
