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
     * @param GeneralGraphicState $targetState
     * @param GeneralGraphicState $previousState
     *
     * @return string[]
     */
    public function visitGeneralGraphics(GeneralGraphicState $targetState, GeneralGraphicState $previousState): array
    {
        // if reference matches, we do not need to do anything
        if ($previousState === $targetState) {
            return [];
        }

        $operators = [];
        if ($previousState->getCurrentTransformationMatrix() !== $targetState->getCurrentTransformationMatrix()) {
            $operators[] = implode(' ', $targetState->getCurrentTransformationMatrix()) . ' cm';
        }

        if ($previousState->getLineWidth() !== $targetState->getLineWidth()) {
            $operators[] = $targetState->getLineWidth() . ' w';
        }

        if ($previousState->getLineCap() !== $targetState->getLineCap()) {
            $operators[] = $targetState->getLineCap() . ' J';
        }

        if ($previousState->getLineJoin() !== $targetState->getLineJoin()) {
            $operators[] = $targetState->getLineJoin() . ' j';
        }

        if ($previousState->getMiterLimit() !== $targetState->getMiterLimit()) {
            $operators[] = $targetState->getMiterLimit() . ' M';
        }

        if ($previousState->getDashArray() !== $targetState->getDashArray() || $previousState->getDashPhase() !== $targetState->getDashPhase()) {
            $operators[] = '[' . implode(',', $targetState->getDashArray()) . '] ' . $targetState->getDashPhase() . ' d';
        }

        return $operators;
    }

    /**
     * @param ColorState $targetState
     * @param ColorState $previousState
     *
     * @return string[]
     */
    public function visitColor(ColorState $targetState, ColorState $previousState): array
    {
        // if reference matches, we do not need to do anything
        if ($previousState === $targetState) {
            return [];
        }

        $operators = [];
        if ($previousState->getRgbStrokingColour() !== $targetState->getRgbStrokingColour()) {
            $operators[] = implode(' ', $targetState->getRgbStrokingColour()) . ' RG';
        }

        if ($previousState->getRgbNonStrokingColour() !== $targetState->getRgbNonStrokingColour()) {
            $operators[] = implode(' ', $targetState->getRgbNonStrokingColour()) . ' rg';
        }

        return $operators;
    }
}
