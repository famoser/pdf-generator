<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Content\Operators\Level;

use PdfGenerator\Backend\Content\Operators\Level\Base\BaseLevel;
use PdfGenerator\Backend\Content\Operators\LevelTransitionVisitor;
use PdfGenerator\Backend\Content\Operators\State\ColorState;
use PdfGenerator\Backend\Content\Operators\State\GeneralGraphicState;

class PageLevel extends BaseLevel
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

    /**
     * @param LevelTransitionVisitor $visitor
     * @param self $previousState
     *
     * @return string[]
     */
    public function accept(LevelTransitionVisitor $visitor, self $previousState): array
    {
        return $visitor->visitPage($this, $previousState);
    }
}
