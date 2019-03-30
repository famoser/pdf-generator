<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Content\Operators;

use PdfGenerator\Backend\Content\Operators\Level\PageLevel;
use PdfGenerator\Backend\Content\Operators\Level\TextLevel;

class LevelTransitionVisitor
{
    /**
     * @var StateTransitionVisitor
     */
    private $stateTransitionVisitor;

    /**
     * LevelTransitionVisitor constructor.
     *
     * @param StateTransitionVisitor $stateTransitionVisitor
     */
    public function __construct(StateTransitionVisitor $stateTransitionVisitor)
    {
        $this->stateTransitionVisitor = $stateTransitionVisitor;
    }

    /**
     * @param PageLevel $targetState
     * @param PageLevel $previousState
     *
     * @return string[]
     */
    public function visitPage(PageLevel $targetState, PageLevel $previousState): array
    {
        return array_merge(
            $this->stateTransitionVisitor->visitColor($targetState->getColorState(), $previousState->getColorState()),
            $this->stateTransitionVisitor->visitGeneralGraphics($targetState->getGeneralGraphicsState(), $previousState->getGeneralGraphicsState()));
    }

    /**
     * @param TextLevel $targetState
     * @param TextLevel $previousState
     *
     * @return string[]
     */
    public function visitText(TextLevel $targetState, TextLevel $previousState): array
    {
        return array_merge(
            $this->stateTransitionVisitor->visitColor($targetState->getColorState(), $previousState->getColorState()),
            $this->stateTransitionVisitor->visitText($targetState->getText(), $previousState->getText()),
            $this->stateTransitionVisitor->visitGeneralGraphics($targetState->getGeneralGraphicsState(), $previousState->getGeneralGraphicsState()));
    }
}
