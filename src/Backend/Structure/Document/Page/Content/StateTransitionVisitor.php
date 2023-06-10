<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Document\Page\Content;

use PdfGenerator\Backend\Structure\Document\DocumentResources;
use PdfGenerator\Backend\Structure\Document\Page\State\ColorState;
use PdfGenerator\Backend\Structure\Document\Page\State\GeneralGraphicState;
use PdfGenerator\Backend\Structure\Document\Page\State\TextState;
use PdfGenerator\Backend\Structure\Document\Page\StateCollections\FullState;

class StateTransitionVisitor
{
    private ?ColorState $appliedColorState = null;

    private ?GeneralGraphicState $appliedGeneralGraphicsState = null;

    private ?TextState $appliedTextState = null;

    public function __construct(private readonly ?FullState $previousState, private readonly DocumentResources $documentResources)
    {
    }

    /**
     * @return string[]
     */
    public function visitColorState(ColorState $targetState): array
    {
        $this->appliedColorState = $targetState;

        $previousState = $this->previousState?->getColorState();

        return $this->getColorOperators($targetState, $previousState);
    }

    /**
     * @return string[]
     */
    public function visitGeneralGraphicState(GeneralGraphicState $targetState): array
    {
        $this->appliedGeneralGraphicsState = $targetState;

        $previousState = $this->previousState?->getGeneralGraphicsState();

        return $this->getGeneralGraphicsOperators($targetState, $previousState);
    }

    /**
     * @return string[]
     */
    public function visitTextState(TextState $targetState): array
    {
        $this->appliedTextState = $targetState;

        $previousState = $this->previousState?->getTextState();

        return $this->getTextOperators($targetState, $previousState);
    }

    public function getAppliedState(): FullState
    {
        return new FullState(
            $this->appliedGeneralGraphicsState ?: $this->previousState?->getGeneralGraphicsState(),
            $this->appliedColorState ?: $this->previousState?->getColorState(),
            $this->appliedTextState ?: $this->previousState?->getTextState()
        );
    }

    /**
     * @return string[]
     */
    private function getTextOperators(TextState $targetState, ?TextState $previousState): array
    {
        // if reference matches, we do not need to do anything
        if ($previousState === $targetState) {
            return [];
        }

        $operators = [];
        if ($previousState?->getFont() !== $targetState->getFont() || $previousState?->getFontSize() !== $targetState->getFontSize()) {
            $font = $this->documentResources->getFont($targetState->getFont());
            $operators[] = '/'.$font->getIdentifier().' '.$targetState->getFontSize().' Tf';
        }

        if (!$previousState) {
            $previousState = new TextState($targetState->getFont(), $targetState->getFontSize());
        }

        if ($previousState->getCharSpace() !== $targetState->getCharSpace()) {
            $operators[] = $targetState->getCharSpace().' Tc';
        }

        if ($previousState->getWordSpace() !== $targetState->getWordSpace()) {
            $operators[] = $targetState->getWordSpace().' Tw';
        }

        if ($previousState->getScale() !== $targetState->getScale()) {
            $operators[] = $targetState->getScale().' Tz';
        }

        if ($previousState->getLeading() !== $targetState->getLeading()) {
            $operators[] = $targetState->getLeading().' TL';
        }

        if ($previousState->getRenderMode() !== $targetState->getRenderMode()) {
            $operators[] = $targetState->getRenderMode().' Tr';
        }

        if ($previousState->getRise() !== $targetState->getRise()) {
            $operators[] = $targetState->getRise().' Ts';
        }

        return $operators;
    }

    /**
     * @return string[]
     */
    private function getGeneralGraphicsOperators(GeneralGraphicState $targetState, ?GeneralGraphicState $previousState): array
    {
        // if reference matches, we do not need to do anything
        if ($previousState === $targetState) {
            return [];
        }

        $operators = [];

        if (!$previousState) {
            $previousState = new GeneralGraphicState();
        }

        if ($previousState->getLineWidth() !== $targetState->getLineWidth()) {
            $operators[] = $targetState->getLineWidth().' w';
        }

        if ($previousState->getLineCap() !== $targetState->getLineCap()) {
            $operators[] = $targetState->getLineCap().' J';
        }

        if ($previousState->getLineJoin() !== $targetState->getLineJoin()) {
            $operators[] = $targetState->getLineJoin().' j';
        }

        if ($previousState->getMiterLimit() !== $targetState->getMiterLimit()) {
            $operators[] = $targetState->getMiterLimit().' M';
        }

        if ($previousState->getDashArray() !== $targetState->getDashArray() || $previousState->getDashPhase() !== $targetState->getDashPhase()) {
            $operators[] = '['.implode(',', $targetState->getDashArray()).'] '.$targetState->getDashPhase().' d';
        }

        return $operators;
    }

    /**
     * @return string[]
     */
    private function getColorOperators(ColorState $targetState, ?ColorState $previousState): array
    {
        // if reference matches, we do not need to do anything
        if ($previousState === $targetState) {
            return [];
        }

        if (!$previousState) {
            $previousState = new ColorState();
        }

        $operators = [];
        if ($previousState->getRgbStrokingColour() !== $targetState->getRgbStrokingColour()) {
            $operators[] = implode(' ', $targetState->getRgbStrokingColour()).' RG';
        }

        if ($previousState->getRgbNonStrokingColour() !== $targetState->getRgbNonStrokingColour()) {
            $operators[] = implode(' ', $targetState->getRgbNonStrokingColour()).' rg';
        }

        return $operators;
    }
}
