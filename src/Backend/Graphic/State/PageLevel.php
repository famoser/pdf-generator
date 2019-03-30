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

class PageLevel
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
     * TextLevel constructor.
     *
     * @param GeneralGraphicState $generalGraphicsState
     * @param ColorState $colorState
     */
    public function __construct(GeneralGraphicState $generalGraphicsState, ColorState $colorState)
    {
        $this->generalGraphicsState = $generalGraphicsState;
        $this->colorState = $colorState;
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
}
