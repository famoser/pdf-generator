<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\Structure\Document\Page;

use Famoser\PdfGenerator\Backend\Structure\Document\DocumentResources;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\ColorState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\GeneralGraphicState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\TextState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\StateCollections\FullState;
use Famoser\PdfGenerator\Utils\TransformationMatrixCalculator;

class StateTransitionVisitor
{
    private const MAX_NUMBER_SIZE = 4;

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
        if ($previousState?->getFont() !== $targetState->getFont() || $previousState->getFontSize() !== $targetState->getFontSize()) {
            $font = $this->documentResources->getFont($targetState->getFont());
            $operators[] = '/'.$font->getIdentifier().' '.$targetState->getFontSize().' Tf';
        }

        if (!$previousState) {
            $previousState = new TextState($targetState->getFont(), $targetState->getFontSize());
        }

        if ($previousState->getCharacterSpacing() !== $targetState->getCharacterSpacing()) {
            $operators[] = self::limitPrecision($targetState->getCharacterSpacing()).' Tc';
        }

        if ($previousState->getWordSpacing() !== $targetState->getWordSpacing()) {
            $operators[] = self::limitPrecision($targetState->getWordSpacing(), 5).' Tw';
        }

        if ($previousState->getScale() !== $targetState->getScale()) {
            $operators[] = self::limitPrecision($targetState->getScale()).' Tz';
        }

        if ($previousState->getLeading() !== $targetState->getLeading()) {
            $operators[] = self::limitPrecision($targetState->getLeading()).' TL';
        }

        if ($previousState->getRenderMode() !== $targetState->getRenderMode()) {
            $operators[] = $targetState->getRenderMode().' Tr';
        }

        if ($previousState->getRise() !== $targetState->getRise()) {
            $operators[] = self::limitPrecision($targetState->getRise()).' Ts';
        }

        return $operators;
    }

    public static function limitPrecision(float $value): string
    {
        $output = (string) $value;
        if (strlen($output) > 5) {
            // restrict places after the dot, as cannot be rendered anyways

            if ($value < 1000) {
                // hence something like 999.8237182 or 0.00231231
                return substr($output, 0, 5);
            } else {
                // remove dot & numbers after (if any)
                $dotPosition = strpos($output, '.');
                if ($dotPosition !== false) {
                    return substr($output, 0, $dotPosition);
                }
            }
        }
        return $output;
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

        if ($previousState->getCurrentTransformationMatrix() !== $targetState->getCurrentTransformationMatrix()) {
            $transformationMatrix = TransformationMatrixCalculator::getTransformationMatrix($previousState->getCurrentTransformationMatrix(), $targetState->getCurrentTransformationMatrix());
            $operators[] = implode(' ', $transformationMatrix).' cm';
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
