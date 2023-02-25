<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Document\Page\State;

use PdfGenerator\Backend\Structure\Document\Page\State\GeneralGraphicState;

class GeneralGraphicStateRepository
{
    /**
     * @var float[]
     */
    private $position = [1, 0, 0, 1, 0, 0];

    /**
     * @var float
     */
    private $lineWidth = 1;

    /**
     * @var GeneralGraphicState
     */
    private $generalGraphicState;

    public function setPosition(float $xStart, float $yStart, float $scaleX = 1, float $scaleY = 1)
    {
        $newPosition = [$scaleX, $scaleY, $xStart, $yStart];
        if ($this->position !== $newPosition) {
            $this->position = $newPosition;
            $this->generalGraphicState = null;
        }
    }

    public function setLineWidth(float $lineWidth)
    {
        if ($this->lineWidth !== $lineWidth) {
            $this->lineWidth = $lineWidth;
            $this->generalGraphicState = null;
        }
    }

    /**
     * @return GeneralGraphicState
     */
    public function getGeneralGraphicState()
    {
        if (null !== $this->generalGraphicState) {
            return $this->generalGraphicState;
        }

        $this->generalGraphicState = new GeneralGraphicState();
        $transformationMatrixShort = [$this->position[0], 0, 0, $this->position[1], $this->position[2], $this->position[3]];
        $this->generalGraphicState->setCurrentTransformationMatrix($transformationMatrixShort);
        $this->generalGraphicState->setLineWidth($this->lineWidth);

        return $this->generalGraphicState;
    }
}
