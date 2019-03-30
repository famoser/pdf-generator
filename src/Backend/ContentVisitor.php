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
     * @return StreamObject
     */
    public function visitTextContent(Content\TextContent $textContent, File $file, Page $page): BaseObject
    {
        $content = [];

        // BT: begin text
        $content[] = 'BT';

        foreach ($textContent->getTextSymbols() as $textSymbol) {
            // gather operators to change to desired state
            $stateTransitionOperators = $this->graphicStateRepository->applyTextLevelState($page, $textSymbol->getTextLevel());

            // gather operators to print the content
            $textOperators = $this->getTextOperators($textSymbol->getContent());

            // print operators to single line
            $operators = array_merge($stateTransitionOperators, $textOperators);
            $content[] = implode(' ', $operators);
        }

        // ET: end text
        $content[] = 'ET';

        return $file->addStreamObject(implode(' ', $content), StreamObject::CONTENT_TYPE_TEXT);
    }

    /**
     * @param Content\ImageContent $param
     * @param File $file
     * @param Page $page
     * @return StreamObject
     */
    public function visitImageContent(Content\ImageContent $param, File $file, Page $page): BaseObject
    {
        $content = [];

        // BT: begin text
        $content[] = 'q';

        // scale by height and translate to x/y
        $content[] = $param->getWidth() . ' 0 0 ' . $param->getHeight() . ' ' . $param->getXCoordinate() . ' ' . $param->getYCoordinate() . ' cm';

        // set font & font size with Tf function
        $content[] = '/' . $param->getImage()->getIdentifier() . ' Do';

        // ET: end text
        $content[] = 'Q';

        return $file->addStreamObject(implode(' ', $content), StreamObject::CONTENT_TYPE_IMAGE);
    }

    /**
     * @param string $text
     * @return string[]
     */
    private function getTextOperators(string $text): string
    {
        // split by newlines
        $cleanedText = str_replace("\n\r", "\n", $text);
        $lines = explode("\n", $cleanedText);

        $printOperators = [];

        // print first line
        $printOperators[] = '(' . $lines[0] . ')Tj';

        // use the ' operator to start a new line before printing
        $lineCount = count($lines);
        for ($i = 1; $i < $lineCount; $i++) {
            $printOperators[] = '(' . $lines[$i] . ')\'';
        }

        return $printOperators;
    }
}
