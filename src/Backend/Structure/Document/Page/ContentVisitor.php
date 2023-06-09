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
use PdfGenerator\Backend\Structure\Document\Page\Content\Base\BaseContent;
use PdfGenerator\Backend\Structure\Document\Page\Content\ImageContent;
use PdfGenerator\Backend\Structure\Document\Page\Content\RectangleContent;
use PdfGenerator\Backend\Structure\Document\Page\Content\StateTransitionVisitor;
use PdfGenerator\Backend\Structure\Document\Page\Content\TextContent;
use PdfGenerator\Backend\Structure\Document\Page\State\ColorState;
use PdfGenerator\Backend\Structure\Document\Page\State\GeneralGraphicState;
use PdfGenerator\Backend\Structure\Document\Page\State\TextState;
use PdfGenerator\Backend\Structure\Document\Page\StateCollections\FullState;

class ContentVisitor
{
    private ?FullState $lastAppliedState = null;

    /**
     * ContentVisitor constructor.
     */
    public function __construct(private readonly DocumentResources $documentResources)
    {
    }

    public function visitTextContent(TextContent $textContent): Content
    {
        $textOperators = $this->getTextOperators($textContent->getLines(), $textContent->getTextState()->getFont());

        $operators = $this->wrapPrintingOperators($textContent, $textOperators);
        // need to add BT before & ET after so text state change operators are valid
        $operators = array_merge(['BT'], $operators, ['ET']);

        return $this->createStreamObject($operators);
    }

    public function visitImageContent(ImageContent $imageContent): Content
    {
        $image = $this->documentResources->getImage($imageContent->getImage());
        $imageOperator = '/'.$image->getIdentifier().' Do';

        $operators = $this->wrapPrintingOperators($imageContent, [$imageOperator]);

        return $this->createStreamObject($operators);
    }

    public function visitRectangleContent(RectangleContent $rectangle): Content
    {
        $paintingOperator = $this->getPaintingOperator($rectangle);
        $rectangleOperator = '0 0 '.$rectangle->getWidth().' '.$rectangle->getHeight().' re '.$paintingOperator;

        $operators = $this->wrapPrintingOperators($rectangle, [$rectangleOperator]);

        return $this->createStreamObject($operators);
    }

    private function wrapPrintingOperators(BaseContent $baseContent, array $printingOperators): array
    {
        $stateTransitionOperators = $this->applyState($baseContent);
        $translationMatrix = $baseContent->getCurrentTransformationMatrix();
        $translationOperator = implode(' ', $translationMatrix).' cm';

        return array_merge($stateTransitionOperators, ['q', $translationOperator], $printingOperators, ['Q']);
    }

    private function createStreamObject(array $operators): Content
    {
        return new Content(implode(' ', $operators));
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

    private function getPaintingOperator(RectangleContent $rectangle): string
    {
        return match ($rectangle->getPaintingMode()) {
            RectangleContent::PAINTING_MODE_STROKE => 's',
            RectangleContent::PAINTING_MODE_FILL => 'f',
            RectangleContent::PAINTING_MODE_STROKE_FILL => 'b',
            default => 'n',
        };
    }

    /**
     * @return string[]
     */
    private function applyState(BaseContent $baseContent): array
    {
        $stateTransitionVisitor = new StateTransitionVisitor($this->lastAppliedState, $this->documentResources);

        /** @var string[] $operators */
        $operators = [];
        foreach ($baseContent->getInfluentialStates() as $influentialState) {
            $operators = array_merge($operators, $influentialState->accept($stateTransitionVisitor));
        }

        $this->lastAppliedState = $stateTransitionVisitor->getAppliedState();

        return $operators;
    }
}
