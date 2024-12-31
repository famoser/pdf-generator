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

use Famoser\PdfGenerator\Backend\Catalog\Content;
use Famoser\PdfGenerator\Backend\Structure\Document\DocumentResources;
use Famoser\PdfGenerator\Backend\Structure\Document\Font;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\Content\ImageContent;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\Content\TextContent;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\Content\RectangleContent;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\Base\BaseState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\StateCollections\FullState;

class ContentVisitor
{
    private ?FullState $lastAppliedState = null;

    public function __construct(private readonly DocumentResources $documentResources)
    {
    }

    public function visitRectangleContent(RectangleContent $rectangle): Content
    {
        $operators = $this->applyState($rectangle->getInfluentialStates());

        $paintingModeOperator = $this->getPaintingModeOperator($rectangle->getPaintingMode());
        $printRectangleOperator = '0 0 ' . $rectangle->getWidth() . ' ' . $rectangle->getHeight() . ' re ' . $paintingModeOperator;
        $operators[] = $printRectangleOperator;

        return $this->createStreamObject($operators);
    }

    public function visitImageContent(ImageContent $imageContent): Content
    {
        $operators = $this->applyState($imageContent->getInfluentialStates());

        $image = $this->documentResources->getImage($imageContent->getImage());
        $printImageOperator = '/' . $image->getIdentifier() . ' Do';
        $operators[] = $printImageOperator;

        return $this->createStreamObject($operators);
    }

    public function visitTextContent(TextContent $textContent): Content
    {
        $operators[] = 'BT';
        /*
        TODO check if needed; unclear in spec where printer starts to print the text
        if ($textContent->getAscender()) {
            $operators[] = '1 0 0 1 0 '.$textContent->getAscender().' Tm';
        }
        */

        foreach ($textContent->getLines() as $lineIndex => $line) {
            // newline if no content
            if (count($line->getSegments()) == 0) {
                $operators[] = '()\'';
                continue;
            }

            foreach ($line->getSegments() as $segmentIndex => $segment) {
                $printOperators = [];
                $text = $this->prepareTextForPrint($segment->getText(), $segment->getTextState()->getFont());
                $needsNewline = $lineIndex > 0 && $segmentIndex === 0;
                if ($needsNewline) {
                    $appliedTextState = $this->lastAppliedState?->getTextState();
                    $targetTextState = $segment->getTextState();

                    if ($line->getOffset() === 0.0) {
                        if ($targetTextState->getWordSpacing() !== $appliedTextState?->getWordSpacing() || $targetTextState->getCharacterSpacing() !== $appliedTextState->getCharacterSpacing()) {
                            $printOperators[] = $targetTextState->getWordSpacing() . ' ' . $targetTextState->getCharacterSpacing() . ' (' . $text . ')"';

                            // avoid automatic state transition operators to reapply new word spacing / character spacing
                            if ($appliedTextState) {
                                $newAppliedTextState = $appliedTextState->cloneWithSpacing($targetTextState->getWordSpacing(), $targetTextState->getCharacterSpacing());
                                $this->lastAppliedState = $this->lastAppliedState->cloneWithTextState($newAppliedTextState);
                            }
                        } else {
                            $printOperators[] = '(' . $text . ')\'';
                        }
                    } else {
                        $printOperators[] = $segment->getTextState()->getLeading() . ' ' . $line->getOffset() . ' TD';
                        $printOperators[] = '(' . $text . ')Tj';

                        // avoid automatic state transition operators to reapply new leading
                        if ($appliedTextState) {
                            $newAppliedTextState = $appliedTextState->cloneWithSpacing($targetTextState->getWordSpacing(), $targetTextState->getCharacterSpacing());
                            $this->lastAppliedState = $this->lastAppliedState->cloneWithTextState($newAppliedTextState);
                        }
                    }
                } else {
                    $printOperators[] = '(' . $text . ')Tj';
                }

                $stateTransitionOperators = $this->applyState($segment->getInfluentialStates());
                $operators = array_merge($operators, $stateTransitionOperators, $printOperators);
            }
        }
        $operators[] = 'ET';

        return $this->createStreamObject($operators);
    }

    /**
     * @param BaseState[] $influentialStates
     *
     * @return string[]
     */
    private function applyState(array $influentialStates): array
    {
        $stateTransitionVisitor = new StateTransitionVisitor($this->lastAppliedState, $this->documentResources);

        /** @var string[] $operators */
        $operators = [];
        foreach ($influentialStates as $influentialState) {
            $operators = array_merge($operators, $influentialState->accept($stateTransitionVisitor));
        }

        $this->lastAppliedState = $stateTransitionVisitor->getAppliedState();

        return $operators;
    }

    /**
     * @param string[] $operators
     */
    private function createStreamObject(array $operators): Content
    {
        return new Content(implode(' ', $operators));
    }

    private function getPaintingModeOperator(int $paintingMode): string
    {
        return match ($paintingMode) {
            RectangleContent::PAINTING_MODE_STROKE => 's',
            RectangleContent::PAINTING_MODE_FILL => 'f',
            RectangleContent::PAINTING_MODE_STROKE_FILL => 'b',
            default => 'n',
        };
    }

    private function prepareTextForPrint(string $text, Font $font): string
    {
        $font = $this->documentResources->getFont($font);

        $encoded = $font->encode($text);

        return $this->escapeReservedCharacters($encoded);
    }

    private function escapeReservedCharacters(string $text): string
    {
        $reserved = ['\\', '(', ')'];

        foreach ($reserved as $entry) {
            $text = str_replace($entry, '\\' . $entry, $text);
        }

        return $text;
    }
}
