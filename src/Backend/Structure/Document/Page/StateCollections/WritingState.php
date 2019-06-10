<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Operators\Level;

use PdfGenerator\Backend\Structure\Operators\Level\Base\BaseStateCollection;
use PdfGenerator\Backend\Structure\Operators\State\Base\BaseState;
use PdfGenerator\Backend\Structure\Operators\State\ColorState;
use PdfGenerator\Backend\Structure\Operators\State\GeneralGraphicState;
use PdfGenerator\Backend\Structure\Operators\State\TextState;

class WritingState extends BaseStateCollection
{
    /**
     * @var GeneralGraphicState
     */
    private $generalGraphicsState;

    /**
     * @var ColorState
     */
    private $colorState;

    /**
     * @var TextState
     */
    private $textState;

    /**
     * TextLevel constructor.
     *
     * @param GeneralGraphicState $generalGraphicsState
     * @param ColorState $colorState
     * @param TextState $textState
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
}
