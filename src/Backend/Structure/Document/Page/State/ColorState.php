<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\Structure\Document\Page\State;

use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\Base\BaseState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\StateTransitionVisitor;

readonly class ColorState extends BaseState
{
    /**
     * @param float[] $rgbStrokingColour    line/border color
     * @param float[] $rgbNonStrokingColour fill color
     *
     * default arguments correspond to PDF defaults
     */
    public function __construct(private array $rgbStrokingColour = [0, 0, 0], private array $rgbNonStrokingColour = [0, 0, 0])
    {
    }

    /**
     * @return float[]
     */
    public function getRgbStrokingColour(): array
    {
        return $this->rgbStrokingColour;
    }

    /**
     * @return float[]
     */
    public function getRgbNonStrokingColour(): array
    {
        return $this->rgbNonStrokingColour;
    }

    /**
     * @return string[]
     */
    public function accept(StateTransitionVisitor $visitor): array
    {
        return $visitor->visitColorState($this);
    }
}
