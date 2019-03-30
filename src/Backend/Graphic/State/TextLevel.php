<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Backend\Graphic\State;

use Backend\Graphic\State\Parameters\ColorState;
use Backend\Graphic\State\Parameters\GeneralGraphicState;
use Backend\Graphic\State\Parameters\TextState;

class TextLevel
{
    /**
     * @var GeneralGraphicState
     */
    private $generalGraphicsState;

    /**
     * @var ColorState
     */
    private $colorState;

    /**
     * @var TextState
     */
    private $textState;

    /**
     * TextLevel constructor.
     *
     * @param GeneralGraphicState $generalGraphicsState
     * @param ColorState $colorState
     * @param TextState $textState
     */
    public function __construct(GeneralGraphicState $generalGraphicsState, ColorState $colorState, TextState $textState)
    {
        $this->generalGraphicsState = $generalGraphicsState;
        $this->colorState = $colorState;
        $this->textState = $textState;
    }

    /**
     * @return GeneralGraphicState
     */
    public function getGeneralGraphicsState(): GeneralGraphicState
    {
        return $this->generalGraphicsState;
    }

    /**
     * @return ColorState
     */
    public function getColorState(): ColorState
    {
        return $this->colorState;
    }

    /**
     * @return TextState
     */
    public function getTextState(): TextState
    {
        return $this->textState;
    }
}
