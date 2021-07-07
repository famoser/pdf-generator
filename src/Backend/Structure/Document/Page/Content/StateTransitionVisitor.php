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

use PdfGenerator\Backend\File\Token\NumberToken;
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
     */
    public function __construct(FullState $state, DocumentResources $documentResources)
    {
        $this->previousState = $state;
        $this->documentResources = $documentResources;
    }

    /**
     * @return string[]
     */
    public function visitColorState(ColorState $targetState)
    {
        $this->appliedColorState = $targetState;

        $previousState = $this->previousState->getColorState();

        return $this->getColorOperators($targetState, $previousState);
    }

    /**
     * @return string[]
     */
    public function visitGeneralGraphicState(GeneralGraphicState $targetState)
    {
        $this->appliedGeneralGraphicsState = $targetState;

        $previousState = $this->previousState->getGeneralGraphicsState();

        return $this->getGeneralGraphicsOperators($targetState, $previousState);
    }

    /**
     * @return string[]
     */
    public function visitTextState(TextState $targetState)
    {
        $this->appliedTextState = $targetState;

        $previousState = $this->previousState->getTextState();

        return $this->getTextOperators($targetState, $previousState);
    }

    public function getAppliedState(): FullState
    {
        return new FullState(
            $this->appliedGeneralGraphicsState ?: $this->previousState->getGeneralGraphicsState(),
            $this->appliedColorState ?: $this->previousState->getColorState(),
            $this->appliedTextState ?: $this->previousState->getTextState()
        );
    }

    /**
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
     * @return string[]
     */
    private function getGeneralGraphicsOperators(GeneralGraphicState $targetState, GeneralGraphicState $previousState): array
    {
        // if reference matches, we do not need to do anything
        if ($previousState === $targetState) {
            return [];
        }

        $operators = [];
        /*
        if ($previousState->getCurrentTransformationMatrix() !== $targetState->getCurrentTransformationMatrix()) {
            $transformationMatrix = $this->transformToCurrentTransformationMatrix($previousState->getCurrentTransformationMatrix(), $targetState->getCurrentTransformationMatrix());
            $operators[] = implode(' ', $transformationMatrix) . ' cm';
        }
        */

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
     */
    private function transformToCurrentTransformationMatrix(array $previousTransformationMatrix, array $targetTransformationMatrix): array
    {
        /*
         * for A = previousTransformationMatrix and B = targetTransformationMatrix
         * we need to calculate the matrix C such that C * A = B
         * C * A is correct per pdf spec 8.3.4 Transformation Matrices
         *
         * C = A**-1 * B
         */
        list($a, $b, $c, $d, $e, $f) = $targetTransformationMatrix;
        list($a2, $b2, $c2, $d2, $e2, $f2) = $this->invertMatrix(...$previousTransformationMatrix);

        /*
         * formula from wolfram alpha
         * {{a, b, 0}, {c, d, 0}, {e, f, 1}} * {{a_2, b_2, 0},{c_2, d_2, 0},{e_2, f_2, 1}}
         */
        $matrix = [
            $a * $a2 + $b * $c2,
            $a * $b2 + $b * $d2,
            $c * $a2 + $d * $c2,
            $c * $b2 + $d * $d2,
            $e * $a2 + $f * $c2 + $e2,
            $e * $b2 + $f * $d2 + $f2,
        ];

        return array_map(function ($value) { return NumberToken::format($value); }, $matrix);
    }

    /**
     * inverts a matrix of the form.
     *
     * ---------
     * | a b 0 |
     * | c d 0 |
     * | e f 1 |
     * ---------
     *
     * @return array
     */
    private function invertMatrix(float $a, float $b, float $c, float $d, float $e, float $f)
    {
        /**
         * formula from wolfram alpha
         * inverse {{a, b, 0}, {c, d, 0}, {e, f, 1}}.
         */
        $divisor1 = $a * $d - $b * $c;
        $divisor2 = $b * $c - $a * $d;

        return [
            $d / $divisor1,
            $b / $divisor2,
            $c / $divisor2,
            $a / $divisor1,
            ($d * $e - $c * $f) / $divisor2,
            ($b * $e - $a * $f) / $divisor1,
        ];
    }

    /**
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
