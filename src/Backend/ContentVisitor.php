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

use PdfGenerator\Backend\Content\Text\TextState;
use PdfGenerator\Backend\File\File;
use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\File\Object\StreamObject;

class ContentVisitor
{
    /**
     * @var TextState|null
     */
    private $activeTextState;

    /**
     * @param Content\TextContent $param
     * @param File $file
     *
     * @return StreamObject
     */
    public function visitTextContent(Content\TextContent $param, File $file): BaseObject
    {
        $content = [];

        // BT: begin text
        $content[] = 'BT';

        foreach ($param->getTextSymbols() as $textSymbol) {
            // gather required operators to print text as expected
            $stateTransitionOperators = $this->transitionToState($textSymbol->getTextState());
            $positionOperator = $param->getXCoordinate() . ' ' . $param->getYCoordinate() . ' Td';
            $printOperator = '(' . $textSymbol->getContent() . ')Tj';

            // merge operators to single line
            $operators = array_merge($stateTransitionOperators, [$positionOperator, $printOperator]);
            $stateOperators = implode(' ', $operators);

            $content[] = $stateOperators . ' ' . $printOperator;
        }

        // ET: end text
        $content[] = 'ET';

        return $file->addStreamObject(implode(' ', $content), StreamObject::CONTENT_TYPE_TEXT);
    }

    /**
     * @param TextState $targetState
     *
     * @return string[]
     */
    public function transitionToState(TextState $targetState)
    {
    }

    /**
     * @param Content\ImageContent $param
     * @param File $file
     *
     * @return StreamObject
     */
    public function visitImageContent(Content\ImageContent $param, File $file): BaseObject
    {
        $content = [];

        // BT: begin text
        $content[] = 'q';

        // scale by 132 and translate to x/y
        $content[] = $param->getWidth() . ' 0 0 ' . $param->getHeight() . ' ' . $param->getXCoordinate() . ' ' . $param->getYCoordinate() . ' cm';

        // set font & font size with Tf function
        $content[] = '/' . $param->getImage()->getIdentifier() . ' Do';

        // ET: end text
        $content[] = 'Q';

        return $file->addStreamObject(implode(' ', $content), StreamObject::CONTENT_TYPE_TEXT);
    }
}
