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

use PdfGenerator\Backend\Content\Operators\State\ColorState;
use PdfGenerator\Backend\Content\Operators\State\GeneralGraphicState;
use PdfGenerator\Backend\Content\Operators\State\TextState;

class StateTransitionVisitor
{
    /**
     * @param TextState $targetState
     * @param TextState $previousState
     *
     * @return string[]
     */
    public function visitText(TextState $targetState, TextState $previousState): array
    {
        // if reference matches, we do not need to do anything
        if ($previousState === $targetState) {
            return [];
        }

        //if active state is null, we set the active state to the default values.
        $forceApplyFont = false;
        if ($previousState === null) {
            // font has no default, hence we take the font from the target state...
            $previousState = new TextState($targetState->getFont(), $targetState->getFontSize());
            // ... but force it to render as an operator later
            $forceApplyFont = true;
        }

        $operators = [];
        if ($forceApplyFont || $previousState->getFont() !== $targetState->getFont() || $previousState->getFontSize() !== $targetState->getFontSize()) {
            $operators[] = '/' . $targetState->getFont()->getIdentifier() . ' ' . $targetState->getFontSize() . ' Tf';
        }

        if ($previousState->getCharSpace() !== $targetState->getCharSpace()) {
            $operators[] = $targetState->getCharSpace() . ' Tc';
        }

        if ($previousState->getWordSpace() !== $targetState->getWordSpace()) {
            $operators[] = $targetState->getWordSpace() . ' Tw';
        }

        if ($previousState->getScale() !== $targetState->getScale()) {
            $operators[] = $targetState->getScale() . ' Tz';
        }

        if ($previousState->getLeading() !== $targetState->getLeading()) {
            $operators[] = $targetState->getLeading() . ' TL';
        }

        if ($previousState->getRenderMode() !== $targetState->getRenderMode()) {
            $operators[] = $targetState->getRenderMode() . ' Tr';
        }

        if ($previousState->getRise() !== $targetState->getRise()) {
            $operators[] = $targetState->getRise() . ' Ts';
        }

        return $operators;
    }

    /**
     * @param GeneralGraphicState $param
     * @param GeneralGraphicState $previousState
     *
     * @return string[]
     */
    public function visitGeneralGraphics(GeneralGraphicState $param, GeneralGraphicState $previousState): array
    {
    }

    /**
     * @param ColorState $param
     * @param ColorState $previousState
     *
     * @return string[]
     */
    public function visitColor(ColorState $param, ColorState $previousState): array
    {
    }
}
