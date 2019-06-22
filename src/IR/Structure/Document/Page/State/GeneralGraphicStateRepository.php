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
    private $currentTransformationMatrix = [1, 0, 0, 1, 0, 0];

    /**
     * @var float
     */
    private $lineWidth = 1;

    /**
     * @var GeneralGraphicState
     */
    private $generalGraphicState;

    /**
     * @param float $xStart
     * @param float $yStart
     * @param float $scaleX
     * @param float $scaleY
     */
    public function setPosition(float $xStart, float $yStart, float $scaleX = 1, float $scaleY = 1)
    {
        $this->currentTransformationMatrix = [$scaleX, 0, 0, $scaleY, $xStart, $yStart];

        $this->generalGraphicState = null;
    }

    /**
     * @param float $lineWidth
     */
    public function setLineWidth(float $lineWidth)
    {
        $this->lineWidth = $lineWidth;

        $this->generalGraphicState = null;
    }

    /**
     * @return GeneralGraphicState
     */
    public function getGeneralGraphicState()
    {
        if ($this->generalGraphicState !== null) {
            return $this->generalGraphicState;
        }

        $this->generalGraphicState = new GeneralGraphicState();
        $this->generalGraphicState->setCurrentTransformationMatrix($this->currentTransformationMatrix);
        $this->generalGraphicState->setLineWidth($this->lineWidth);

        return $this->generalGraphicState;
    }
}
