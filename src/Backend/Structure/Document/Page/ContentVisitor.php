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
use PdfGenerator\Backend\Structure\Document\Page\Content\ImageContent;
use PdfGenerator\Backend\Structure\Document\Page\Content\RectangleContent;
use PdfGenerator\Backend\Structure\Document\Page\Content\StateTransitionVisitor;
use PdfGenerator\Backend\Structure\Document\Page\Content\TextContent;
use PdfGenerator\Backend\Structure\Document\Page\StateCollections\FullState;
use PdfGenerator\Backend\Structure\Page\Content\Base\BaseContent;

class ContentVisitor
{
    /**
     * @var FullState
     */
    private $state;

    /**
     * ContentVisitor constructor.
     */
    public function __construct()
    {
        $this->state = FullState::createInitial();
    }

    /**
     * @param TextContent $textContent
     *
     * @return Content
     */
    public function visitTextContent(TextContent $textContent): Content
    {
        // gather operators to change to desired state
        $stateTransitionOperators = $this->applyState($textContent);

        // gather operators to print the content
        $textOperators = $this->getTextOperators($textContent->getLines());

        // create stream object; BT before text and ET after all text
        $operators = array_merge(['BT'], $stateTransitionOperators, $textOperators, ['ET']);

        return $this->createStreamObject($operators);
    }

    /**
     * @param ImageContent $imageContent
     *
     * @return Content
     */
    public function visitImageContent(ImageContent $imageContent): Content
    {
        // gather operators to change to desired state
        $stateTransitionOperators = $this->applyState($imageContent);

        // gather operators to print the content
        $imageOperator = '/' . $imageContent->getImage()->getIdentifier() . ' Do';

        // create stream object
        $operators = array_merge($stateTransitionOperators, [$imageOperator]);

        return $this->createStreamObject($operators);
    }

    /**
     * @param RectangleContent $rectangle
     *
     * @return Content
     */
    public function visitRectangleContent(RectangleContent $rectangle): Content
    {
        // gather operators to change to desired state
        $stateTransitionOperators = $this->applyState($rectangle);

        // gather operators to print the content
        $paintingOperator = $this->getPaintingOperator($rectangle);
        $imageOperator = '0 0 ' . $rectangle->getWidth() . ' ' . $rectangle->getHeight() . ' re ' . $paintingOperator;

        // create stream object
        $operators = array_merge($stateTransitionOperators, [$imageOperator]);

        return $this->createStreamObject($operators);
    }

    /**
     * @param array $operators
     *
     * @return Content
     */
    private function createStreamObject(array $operators): Content
    {
        return new Content(implode(' ', $operators), Content::CONTENT_TYPE_TEXT);
    }

    /**
     * @param array $lines
     *
     * @return string[]
     */
    private function getTextOperators(array $lines): array
    {
        $printOperators = [];

        // print first line
        $printOperators[] = '(' . $lines[0] . ')Tj';

        // use the ' operator to start a new line before printing
        $lineCount = \count($lines);
        for ($i = 1; $i < $lineCount; ++$i) {
            $printOperators[] = '(' . $lines[$i] . ')\'';
        }

        return $printOperators;
    }

    /**
     * @param RectangleContent $rectangle
     *
     * @return string
     */
    private function getPaintingOperator(RectangleContent $rectangle): string
    {
        switch ($rectangle->getPaintingMode()) {
            case RectangleContent::PAINTING_MODE_STROKE:
                return 's';
            case RectangleContent::PAINTING_MODE_FILL:
                return 'f';
            case RectangleContent::PAINTING_MODE_STROKE_FILL:
                return 'b';
            case RectangleContent::PAINTING_MODE_NONE:
            default:
                return 'n';
        }
    }

    /**
     * @param BaseContent $baseContent
     *
     * @return string[]
     */
    private function applyState(BaseContent $baseContent): array
    {
        $stateTransitionVisitor = new StateTransitionVisitor($this->state);

        /** @var string[] $operators */
        $operators = [];
        foreach ($baseContent->getInfluentialStates() as $influentialState) {
            $operators = array_merge($operators, $influentialState->accept($stateTransitionVisitor));
        }

        return $operators;
    }
}
