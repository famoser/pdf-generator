<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend;

use PdfGenerator\Backend\Content\GraphicStateRepository;
use PdfGenerator\Backend\Content\ImageContent;
use PdfGenerator\Backend\Content\Rectangle;
use PdfGenerator\Backend\File\File;
use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\File\Object\StreamObject;
use PdfGenerator\Backend\Structure\Page;

class ContentVisitor
{
    /**
     * @var GraphicStateRepository
     */
    private $graphicStateRepository;

    /**
     * ContentVisitor constructor.
     */
    public function __construct()
    {
        $this->graphicStateRepository = new GraphicStateRepository();
    }

    /**
     * @param Content\TextContent $textContent
     * @param File $file
     * @param Page $page
     *
     * @return StreamObject
     */
    public function visitTextContent(Content\TextContent $textContent, File $file, Page $page): BaseObject
    {
        // gather operators to change to desired state
        $stateTransitionOperators = $this->graphicStateRepository->applyTextLevelState($page, $textContent->getTextLevel());

        // gather operators to print the content
        $textOperators = $this->getTextOperators($textContent->getLines());

        // create stream object; BT before text and ET after all text
        $operators = array_merge(['BT'], $stateTransitionOperators, $textOperators, ['ET']);

        return $this->createStreamObject($file, $operators);
    }

    /**
     * @param Content\ImageContent $imageContent
     * @param File $file
     * @param Page $page
     *
     * @return StreamObject
     */
    public function visitImageContent(Content\ImageContent $imageContent, File $file, Page $page): BaseObject
    {
        // gather operators to change to desired state
        $stateTransitionOperators = $this->graphicStateRepository->applyPageLevelState($page, $imageContent->getPageLevel());

        // gather operators to print the content
        $imageOperator = '/' . $imageContent->getImage()->getIdentifier() . ' Do';

        // create stream object
        $operators = array_merge($stateTransitionOperators, [$imageOperator]);

        return $this->createStreamObject($file, $operators);
    }

    /**
     * @param Content\Rectangle $cell
     * @param File $file
     * @param Page $page
     *
     * @return StreamObject
     */
    public function visitRectangle(Content\Rectangle $cell, File $file, Page $page): BaseObject
    {
        // gather operators to change to desired state
        $stateTransitionOperators = $this->graphicStateRepository->applyPageLevelState($page, $cell->getPageLevel());

        // gather operators to print the content
        $paintingOperator = $this->getPaintingOperator($cell);
        $imageOperator = '0 0 ' . $cell->getWidth() . ' ' . $cell->getHeight() . ' re ' . $paintingOperator;

        // create stream object
        $operators = array_merge($stateTransitionOperators, [$imageOperator]);

        return $this->createStreamObject($file, $operators);
    }

    /**
     * @param File $file
     * @param array $operators
     *
     * @return StreamObject
     */
    private function createStreamObject(File $file, array $operators): StreamObject
    {
        return $file->addStreamObject(implode(' ', $operators), StreamObject::CONTENT_TYPE_TEXT);
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
     * @param Rectangle $rectangle
     *
     * @return string
     */
    private function getPaintingOperator(Rectangle $rectangle): string
    {
        switch ($rectangle->getPaintingMode()) {
            case Rectangle::PAINTING_MODE_STROKE:
                return 's';
            case Rectangle::PAINTING_MODE_FILL:
                return 'f';
            case Rectangle::PAINTING_MODE_STROKE_FILL:
                return 'b';
            default:
                return 's';
        }
    }
}
