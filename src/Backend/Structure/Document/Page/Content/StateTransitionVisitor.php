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
    /**
     * @var FullState
     */
    private $previousState;

    /**
     * @var ColorState
     */
    private $appliedColorState;

    /**
     * @var GeneralGraphicState
     */
    private $appliedGeneralGraphicsState;

    /**
     * @var TextState
     */
    private $appliedTextState;

    /**
     * @var DocumentResources
     */
    private $documentResources;

    /**
     * StateTransitionVisitor constructor.
     *
     * @param FullState $state
     * @param DocumentResources $documentResources
     */
    public function __construct(FullState $state, DocumentResources $documentResources)
    {
        $this->previousState = $state;
        $this->documentResources = $documentResources;
    }

    /**
     * @param ColorState $targetState
     *
     * @return string[]
     */
    public function visitColorState(ColorState $targetState)
    {
        $this->appliedColorState = $targetState;

        $previousState = $this->previousState->getColorState();

        return $this->getColorOperators($targetState, $previousState);
    }

    /**
     * @param GeneralGraphicState $targetState
     *
     * @return string[]
     */
    public function visitGeneralGraphicState(GeneralGraphicState $targetState)
    {
        $this->appliedGeneralGraphicsState = $targetState;

        $previousState = $this->previousState->getGeneralGraphicsState();

        return $this->getGeneralGraphicsOperators($targetState, $previousState);
    }

    /**
     * @param TextState $targetState
     *
     * @return string[]
     */
    public function visitTextState(TextState $targetState)
    {
        $this->appliedTextState = $targetState;

        $previousState = $this->previousState->getTextState();

        return $this->getTextOperators($targetState, $previousState);
    }

    /**
     * @return FullState
     */
    public function getAppliedState(): FullState
    {
        return new FullState(
            $this->appliedGeneralGraphicsState ? $this->appliedGeneralGraphicsState : $this->previousState->getGeneralGraphicsState(),
            $this->appliedColorState ? $this->appliedColorState : $this->previousState->getColorState(),
            $this->appliedTextState ? $this->appliedTextState : $this->previousState->getTextState(),
        );
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
            $font = $this->documentResources->getFont($targetState->getFont());
            $operators[] = '/' . $font->getIdentifier() . ' ' . $targetState->getFontSize() . ' Tf';
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
            var_dump('previous: ' . implode(', ', $previousState->getCurrentTransformationMatrix()));
            var_dump('target: ' . implode(', ', $targetState->getCurrentTransformationMatrix()));
            $transformationMatrix = $this->transformToCurrentTransformationMatrix($previousState->getCurrentTransformationMatrix(), $targetState->getCurrentTransformationMatrix());
            $operators[] = implode(' ', $transformationMatrix) . ' cm';
            var_dump('result: ' . implode(', ', $transformationMatrix));
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
     * calculates a matrix for the diff between previous & current transformation matrix.
     *
     * @param array $previousTransformationMatrix
     * @param array $targetTransformationMatrix
     *
     * @return array
     */
    private function transformToCurrentTransformationMatrix(array $previousTransformationMatrix, array $targetTransformationMatrix): array
    {
        list($scaleX, $skewY, $skewX, $scaleY, $xPos, $yPos) = $previousTransformationMatrix;
        list($scaleX2, $skewY2, $skewX2, $scaleY2, $xPos2, $yPos2) = $targetTransformationMatrix;

        return [
            $scaleX2 - $scaleX,
            $skewY2 - $skewY,
            $skewX2 - $skewX,
            $scaleY2 - $scaleY,
            $xPos2 - $xPos,
            $yPos2 - $yPos,
        ];
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
