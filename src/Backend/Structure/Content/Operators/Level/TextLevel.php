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
use PdfGenerator\Backend\Content\Operators\State\TextState;

class TextLevel extends BaseLevel
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
    private $text;

    /**
     * TextLevel constructor.
     *
     * @param GeneralGraphicState $generalGraphicsState
     * @param ColorState $colorState
     * @param TextState $text
     */
    public function __construct(GeneralGraphicState $generalGraphicsState, ColorState $colorState, TextState $text)
    {
        $this->generalGraphicsState = $generalGraphicsState;
        $this->colorState = $colorState;
        $this->text = $text;
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
    public function getText(): TextState
    {
        return $this->text;
    }

    /**
     * @param LevelTransitionVisitor $visitor
     * @param self $previousState
     *
     * @return string[]
     */
    public function accept(LevelTransitionVisitor $visitor, $previousState): array
    {
        return $visitor->visitText($this, $previousState);
    }

    /**
     * @param PageLevel $pageLevel
     */
    public function applyStateFromPage(PageLevel $pageLevel)
    {
        $this->colorState = $pageLevel->getColorState();
        $this->generalGraphicsState = $pageLevel->getGeneralGraphicsState();
    }
}
