<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Block;

use PdfGenerator\Frontend\Block\Base\BaseBlock;
use PdfGenerator\Frontend\Block\Base\BlocksTrait;
use PdfGenerator\Frontend\Block\Base\FlowTrait;
use PdfGenerator\Frontend\Block\Base\PerpendicularFlowTrait;

class Grid extends BaseBlock
{
    use FlowTrait;
    use PerpendicularFlowTrait;
    use BlocksTrait;

    /**
     * @param float[]|null $perpendicularDimensions
     * @param float[]      $dimensions
     */
    public function __construct(string $direction = self::DIRECTION_ROW, float $gap = 0, float $perpendicularGap = 0, array $dimensions = null, array $perpendicularDimensions = null)
    {
        parent::__construct();
        $this->setDirection($direction);
        $this->setGap($gap);
        $this->setPerpendicularGap($perpendicularGap);
        $this->setDimensions($dimensions);
        $this->setPerpendicularDimensions($perpendicularDimensions);
    }
}
