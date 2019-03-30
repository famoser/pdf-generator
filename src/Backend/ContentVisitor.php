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
        // if reference matches, we do not need to do anything
        if ($this->activeTextState === $targetState) {
            return [];
        }

        //if active state is null, we set the active state to the default values.
        $forceApplyFont = false;
        if ($this->activeTextState === null) {
            // font has no default, hence we take the font from the target state...
            $this->activeTextState = new TextState($targetState->getFont(), $targetState->getFontSize());
            // ... but force it to render as an operator later
            $forceApplyFont = true;
        }

        $operators = [];
        if ($forceApplyFont || $this->activeTextState->getFont() !== $targetState->getFont() || $this->activeTextState->getFontSize() !== $targetState->getFontSize()) {
            $operators[] = '/' . $targetState->getFont()->getIdentifier() . ' ' . $targetState->getFontSize() . ' Tf';
        }

        if ($this->activeTextState->getCharSpace() !== $targetState->getCharSpace()) {
            $operators[] = $targetState->getCharSpace() . ' Tc';
        }

        if ($this->activeTextState->getWordSpace() !== $targetState->getWordSpace()) {
            $operators[] = $targetState->getWordSpace() . ' Tw';
        }

        if ($this->activeTextState->getScale() !== $targetState->getScale()) {
            $operators[] = $targetState->getScale() . ' Tz';
        }

        if ($this->activeTextState->getLeading() !== $targetState->getLeading()) {
            $operators[] = $targetState->getLeading() . ' TL';
        }

        if ($this->activeTextState->getRenderMode() !== $targetState->getRenderMode()) {
            $operators[] = $targetState->getRenderMode() . ' Tr';
        }

        if ($this->activeTextState->getRise() !== $targetState->getRise()) {
            $operators[] = $targetState->getRise() . ' Ts';
        }

        $this->activeTextState = $targetState;

        return $operators;
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
