<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Document\Page\StateCollections;

use PdfGenerator\Backend\Structure\Document\Page\State\Base\BaseState;
use PdfGenerator\Backend\Structure\Document\Page\State\ColorState;
use PdfGenerator\Backend\Structure\Document\Page\State\GeneralGraphicState;
use PdfGenerator\Backend\Structure\Document\Page\State\TextState;
use PdfGenerator\Backend\Structure\Document\Page\StateCollections\Base\BaseStateCollection;

class WritingState extends BaseStateCollection
{
    private GeneralGraphicState $generalGraphicsState;

    private ColorState $colorState;

    private TextState $textState;

    /**
     * TextLevel constructor.
     */
    public function __construct(GeneralGraphicState $generalGraphicsState, ColorState $colorState, TextState $textState)
    {
        $this->generalGraphicsState = $generalGraphicsState;
        $this->colorState = $colorState;
        $this->textState = $textState;
    }

    /**
     * @return BaseState[]
     */
    public function getState(): array
    {
        return [$this->generalGraphicsState, $this->colorState, $this->textState];
    }

    public function getGeneralGraphicsState(): GeneralGraphicState
    {
        return $this->generalGraphicsState;
    }

    public function getTextState(): TextState
    {
        return $this->textState;
    }
}
