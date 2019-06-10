<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure;

use PdfGenerator\Backend\Structure\Operators\State\ColorState;
use PdfGenerator\Backend\Structure\Operators\State\GeneralGraphicState;
use PdfGenerator\Backend\Structure\Operators\State\TextState;
use PdfGenerator\Backend\Structure\StateCollections\FullState;

class StateTransitionVisitor
{
    /**
     * @var FullState
     */
    private $previousState;

    /**
     * StateTransitionVisitor constructor.
     *
     * @param FullState $state
     */
    public function __construct(FullState $state)
    {
        $this->previousState = $state;
    }

    /**
     * @param ColorState $targetState
     *
     * @return string[]
     */
    public function visitColorState(ColorState $targetState)
    {
        return $this->getColorOperators($targetState, $this->previousState->getColorState());
    }

    /**
     * @param GeneralGraphicState $targetState
     *
     * @return string[]
     */
    public function visitGeneralGraphicState(GeneralGraphicState $targetState)
    {
        return $this->getGeneralGraphicsOperators($targetState, $this->previousState->getGeneralGraphicsState());
    }

    /**
     * @param TextState $targetState
     *
     * @return string[]
     */
    public function visitTextState(TextState $targetState)
    {
        return $this->getTextOperators($targetState, $this->previousState->getTextState());
    }

    /**
     * @param TextState $targetState
     * @param TextState $previousState
     *
     * @return string[]
     */
    private function getTextOperators(TextState $targetState, TextState $previousState): array
    {
        // if reference matches, we do not need to do anything
        if ($previousState === $targetState) {
            return [];
        }

        $operators = [];
        if ($previousState->getFont() !== $targetState->getFont() || $previousState->getFontSize() !== $targetState->getFontSize()) {
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
    private function getGeneralGraphicsOperators(GeneralGraphicState $targetState, GeneralGraphicState $previousState): array
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
    private function getColorOperators(ColorState $targetState, ColorState $previousState): array
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
