<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Content\Operators\Level;

use PdfGenerator\Backend\Content\Operators\Level\Base\BaseStateCollection;
use PdfGenerator\Backend\Content\Operators\State\Base\BaseState;
use PdfGenerator\Backend\Content\Operators\State\ColorState;
use PdfGenerator\Backend\Content\Operators\State\GeneralGraphicState;

class DrawingState extends BaseStateCollection
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
     * TextLevel constructor.
     *
     * @param GeneralGraphicState $generalGraphicsState
     * @param ColorState $colorState
     */
    public function __construct(GeneralGraphicState $generalGraphicsState, ColorState $colorState)
    {
        $this->generalGraphicsState = $generalGraphicsState;
        $this->colorState = $colorState;
    }

    /**
     * @return BaseState[]
     */
    public function getState(): array
    {
        return [$this->generalGraphicsState, $this->colorState];
    }
}
