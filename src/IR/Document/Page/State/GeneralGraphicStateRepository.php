<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Document\Page\State;

use PdfGenerator\Backend\Structure\Document\Page\State\GeneralGraphicState;

class GeneralGraphicStateRepository
{
    /**
     * @var float[]
     */
    private array $position = [1, 0, 0, 1, 0, 0];

    private float $lineWidth = 1;

    private ?GeneralGraphicState $generalGraphicState = null;

    public function setPosition(float $xStart, float $yStart, float $scaleX = 1, float $scaleY = 1): void
    {
        $newPosition = [$scaleX, $scaleY, $xStart, $yStart];
        if ($this->position !== $newPosition) {
            $this->position = $newPosition;
            $this->generalGraphicState = null;
        }
    }

    public function setLineWidth(float $lineWidth): void
    {
        if ($this->lineWidth !== $lineWidth) {
            $this->lineWidth = $lineWidth;
            $this->generalGraphicState = null;
        }
    }

    public function getGeneralGraphicState(): GeneralGraphicState
    {
        if (null !== $this->generalGraphicState) {
            return $this->generalGraphicState;
        }

        $transformationMatrixShort = [$this->position[0], 0, 0, $this->position[1], $this->position[2], $this->position[3]];
        $this->generalGraphicState = new GeneralGraphicState($transformationMatrixShort, $this->lineWidth);

        return $this->generalGraphicState;
    }
}
