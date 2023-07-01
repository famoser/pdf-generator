<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Document\Page;

use PdfGenerator\Backend\Catalog\Content;
use PdfGenerator\Backend\Structure\Document\DocumentResources;
use PdfGenerator\Backend\Structure\Document\Font;
use PdfGenerator\Backend\Structure\Document\Page\Content\ImageContent;
use PdfGenerator\Backend\Structure\Document\Page\Content\ParagraphContent;
use PdfGenerator\Backend\Structure\Document\Page\Content\RectangleContent;
use PdfGenerator\Backend\Structure\Document\Page\Content\TextContent;
use PdfGenerator\Backend\Structure\Document\Page\StateCollections\FullState;

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
        $printRectangleOperator = '0 0 '.$rectangle->getWidth().' '.$rectangle->getHeight().' re '.$paintingModeOperator;
        $operators[] = $printRectangleOperator;

        return $this->createStreamObject($operators);
    }

    public function visitImageContent(ImageContent $imageContent): Content
    {
        $operators = $this->applyState($imageContent->getInfluentialStates());

        $image = $this->documentResources->getImage($imageContent->getImage());
        $printImageOperator = '/'.$image->getIdentifier().' Do';
        $operators[] = $printImageOperator;

        return $this->createStreamObject($operators);
    }

    public function visitTextContent(TextContent $textContent): Content
    {
        $operators = $this->applyState($textContent->getInfluentialStates());

        $textOperators = $this->getTextOperators($textContent->getLines(), $textContent->getTextState()->getFont());
        $operators = array_merge($operators, ['BT'], $textOperators, ['ET']);

        return $this->createStreamObject($operators);
    }

    public function visitParagraphContent(ParagraphContent $paragraph): Content
    {
        $operators = $this->applyState($paragraph->getInfluentialStates());

        $operators[] = 'BT';
        foreach ($paragraph->getPhrases() as $phrase) {
            $stateTransitionOperators = $this->applyState($phrase->getInfluentialStates());
            $textOperators = $this->getTextOperators($phrase->getLines(), $phrase->getTextState()->getFont());
            $operators = array_merge($operators, $stateTransitionOperators, $textOperators);
        }
        $operators[] = 'ET';

        return $this->createStreamObject($operators);
    }

    /**
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

    /**
     * @return string[]
     */
    private function getTextOperators(array $lines, Font $font): array
    {
        $printOperators = [];

        // print first line
        $printOperators[] = '('.$this->prepareTextForPrint($lines[0], $font).')Tj';

        // use the ' operator to start a new line before printing
        $lineCount = \count($lines);
        for ($i = 1; $i < $lineCount; ++$i) {
            $printOperators[] = '('.$this->prepareTextForPrint($lines[$i], $font).')\'';
        }

        return $printOperators;
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
            $text = str_replace($entry, '\\'.$entry, $text);
        }

        return $text;
    }
}
